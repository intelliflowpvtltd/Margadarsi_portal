<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Collection;

class DuplicateDetectionService
{
    const MATCH_EXACT = 'exact';
    const MATCH_FUZZY = 'fuzzy';
    const MATCH_NAME = 'name';
    const MATCH_CROSS_PROJECT = 'cross_project';

    public function checkDuplicate(array $data, int $companyId, ?int $excludeLeadId = null): array
    {
        $mobile = Lead::normalizePhone($data['mobile'] ?? '');
        $altMobile = isset($data['alt_mobile']) ? Lead::normalizePhone($data['alt_mobile']) : null;
        $whatsapp = isset($data['whatsapp']) ? Lead::normalizePhone($data['whatsapp']) : null;
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $projectId = $data['project_id'] ?? null;

        $result = [
            'is_duplicate' => false,
            'match_type' => null,
            'matched_lead' => null,
            'message' => null,
            'action' => 'create',
        ];

        // Layer 1: Exact phone match
        $exactMatch = $this->findExactPhoneMatch($companyId, $mobile, $altMobile, $whatsapp, $excludeLeadId);
        if ($exactMatch) {
            return [
                'is_duplicate' => true,
                'match_type' => self::MATCH_EXACT,
                'matched_lead' => $exactMatch,
                'message' => "Lead already exists: {$exactMatch->name} added on {$exactMatch->created_at->format('d M Y')} via {$exactMatch->leadSource?->name}",
                'action' => 'block',
            ];
        }

        // Layer 2: Fuzzy phone match
        $fuzzyMatch = $this->findFuzzyPhoneMatch($companyId, $mobile, $excludeLeadId);
        if ($fuzzyMatch) {
            return [
                'is_duplicate' => false,
                'match_type' => self::MATCH_FUZZY,
                'matched_lead' => $fuzzyMatch,
                'message' => "Similar phone found: {$fuzzyMatch->mobile} (possible typo)",
                'action' => 'warn',
            ];
        }

        // Layer 3: Name + Email match
        if ($name && $email) {
            $nameMatch = $this->findNameEmailMatch($companyId, $name, $email, $excludeLeadId);
            if ($nameMatch) {
                return [
                    'is_duplicate' => false,
                    'match_type' => self::MATCH_NAME,
                    'matched_lead' => $nameMatch,
                    'message' => "Similar name found: {$nameMatch->name} in {$nameMatch->city}",
                    'action' => 'soft_warn',
                ];
            }
        }

        // Layer 4: Cross-project check
        if ($projectId) {
            $crossProjectMatch = $this->findCrossProjectMatch($companyId, $mobile, $projectId, $excludeLeadId);
            if ($crossProjectMatch) {
                return [
                    'is_duplicate' => false,
                    'match_type' => self::MATCH_CROSS_PROJECT,
                    'matched_lead' => $crossProjectMatch,
                    'message' => "Customer also interested in {$crossProjectMatch->project?->name} - leads will be linked",
                    'action' => 'create_and_link',
                ];
            }
        }

        return $result;
    }

    public function checkCpOwnership(array $data, int $companyId): array
    {
        $mobile = Lead::normalizePhone($data['mobile'] ?? '');
        
        $existingLead = Lead::where('company_id', $companyId)
            ->where(function ($q) use ($mobile) {
                $q->whereRaw("RIGHT(mobile, 10) = ?", [$mobile])
                  ->orWhereRaw("RIGHT(alt_mobile, 10) = ?", [$mobile])
                  ->orWhereRaw("RIGHT(whatsapp, 10) = ?", [$mobile]);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$existingLead) {
            return ['allowed' => true, 'message' => null];
        }

        $hoursSinceCreation = $existingLead->created_at->diffInHours(now());

        // 48-hour ownership rule
        if ($hoursSinceCreation < 48) {
            return [
                'allowed' => false,
                'message' => "Lead recently added by {$existingLead->leadSource?->name} on {$existingLead->created_at->format('d M Y H:i')}. CP cannot claim within 48 hours.",
                'existing_lead' => $existingLead,
            ];
        }

        // Check if dormant - allow reactivation
        if ($existingLead->is_dormant) {
            return [
                'allowed' => true,
                'message' => "Dormant lead exists. Will be reactivated as CP lead.",
                'existing_lead' => $existingLead,
                'action' => 'reactivate',
            ];
        }

        return ['allowed' => true, 'message' => null];
    }

    protected function findExactPhoneMatch(int $companyId, string $mobile, ?string $altMobile, ?string $whatsapp, ?int $excludeId): ?Lead
    {
        $phones = array_filter([$mobile, $altMobile, $whatsapp]);
        
        return Lead::where('company_id', $companyId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($phones) {
                foreach ($phones as $phone) {
                    $q->orWhereRaw("RIGHT(mobile, 10) = ?", [$phone])
                      ->orWhereRaw("RIGHT(alt_mobile, 10) = ?", [$phone])
                      ->orWhereRaw("RIGHT(whatsapp, 10) = ?", [$phone]);
                }
            })
            ->with(['leadSource', 'project'])
            ->first();
    }

    protected function findFuzzyPhoneMatch(int $companyId, string $mobile, ?int $excludeId): ?Lead
    {
        if (strlen($mobile) < 10) {
            return null;
        }

        // Check for transposed digits (swap adjacent pairs)
        $variants = $this->generatePhoneVariants($mobile);
        
        return Lead::where('company_id', $companyId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($variants) {
                foreach ($variants as $variant) {
                    $q->orWhereRaw("RIGHT(mobile, 10) = ?", [$variant]);
                }
            })
            ->first();
    }

    protected function findNameEmailMatch(int $companyId, string $name, string $email, ?int $excludeId): ?Lead
    {
        $normalizedName = $this->normalizeName($name);
        $emailPrefix = explode('@', $email)[0];

        return Lead::where('company_id', $companyId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($normalizedName, $emailPrefix) {
                $q->whereRaw("LOWER(REPLACE(REPLACE(REPLACE(name, 'Mr. ', ''), 'Mrs. ', ''), 'Dr. ', '')) LIKE ?", ["%{$normalizedName}%"])
                  ->orWhereRaw("SPLIT_PART(email, '@', 1) = ?", [$emailPrefix]);
            })
            ->first();
    }

    protected function findCrossProjectMatch(int $companyId, string $mobile, int $currentProjectId, ?int $excludeId): ?Lead
    {
        return Lead::where('company_id', $companyId)
            ->where('project_id', '!=', $currentProjectId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereRaw("RIGHT(mobile, 10) = ?", [$mobile])
            ->with('project')
            ->first();
    }

    protected function generatePhoneVariants(string $phone): array
    {
        $variants = [];
        $chars = str_split($phone);
        
        // Generate transposition variants (swap adjacent digits)
        for ($i = 0; $i < strlen($phone) - 1; $i++) {
            $variant = $chars;
            $temp = $variant[$i];
            $variant[$i] = $variant[$i + 1];
            $variant[$i + 1] = $temp;
            $variants[] = implode('', $variant);
        }
        
        return $variants;
    }

    protected function normalizeName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/^(mr\.?|mrs\.?|ms\.?|dr\.?)\s*/i', '', $name);
        $name = trim($name);
        return $name;
    }

    public function findPotentialDuplicates(int $companyId, int $limit = 100): Collection
    {
        return Lead::where('company_id', $companyId)
            ->where('is_duplicate', false)
            ->selectRaw("mobile, COUNT(*) as count")
            ->groupBy('mobile')
            ->havingRaw('COUNT(*) > 1')
            ->limit($limit)
            ->get();
    }
}
