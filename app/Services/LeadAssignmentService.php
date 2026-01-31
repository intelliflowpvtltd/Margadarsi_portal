<?php

namespace App\Services;

use App\Models\AssignmentRule;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Support\Facades\DB;

class LeadAssignmentService
{
    /**
     * Assign a lead to a user using round-robin logic.
     * 
     * Priority:
     * 1. Assignment rules (if configured)
     * 2. Project default team (if exists)
     * 3. Direct project users round-robin (PRIMARY METHOD)
     * 4. Company catch-all team
     */
    public function assignLead(Lead $lead): ?User
    {
        return DB::transaction(function () use ($lead) {
            // Step 1: Find matching assignment rule
            $rule = $this->findMatchingRule($lead);
            
            // Step 2: Determine target from rule
            $assignee = null;
            $team = null;
            
            if ($rule) {
                $lead->assignment_rule_id = $rule->id;
                
                if ($rule->assign_to_user_id) {
                    $assignee = User::find($rule->assign_to_user_id);
                    $team = $this->getUserTeam($assignee, $lead->project_id);
                } elseif ($rule->assign_to_team_id) {
                    $team = Team::find($rule->assign_to_team_id);
                    $assignee = $this->roundRobinInTeam($team);
                }
            }
            
            // Step 3: Fallback to project default team
            if (!$assignee) {
                $team = Team::forProject($lead->project_id)->active()->default()->first();
                if ($team) {
                    $assignee = $this->roundRobinInTeam($team);
                }
            }
            
            // Step 4: PRIMARY - Direct project users round-robin
            // This handles: Project A has User 1, 2, 3 → rotate leads among them
            if (!$assignee) {
                $assignee = $this->roundRobinInProject($lead->project_id);
            }
            
            // Step 5: Fallback to company catch-all team
            if (!$assignee) {
                $team = Team::forCompany($lead->company_id)->active()->whereNull('project_id')->first();
                if ($team) {
                    $assignee = $this->roundRobinInTeam($team);
                }
            }
            
            if ($assignee) {
                $this->performAssignment($lead, $assignee, $team);
                $this->setSlaTimer($lead);
            }
            
            return $assignee;
        });
    }

    protected function findMatchingRule(Lead $lead): ?AssignmentRule
    {
        $rules = AssignmentRule::where('company_id', $lead->company_id)
            ->active()
            ->ordered()
            ->get();

        foreach ($rules as $rule) {
            if ($rule->matches($lead)) {
                return $rule;
            }
        }

        return null;
    }

    protected function roundRobinInTeam(Team $team): ?User
    {
        $member = $team->teamMembers()
            ->where('is_available', true)
            ->whereRaw('current_active_leads < max_active_leads')
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->orderByRaw('current_active_leads / NULLIF(assignment_weight, 0) ASC')
            ->orderBy('last_assigned_at', 'asc')
            ->first();

        if ($member) {
            // Check working hours and days
            if (!$member->isWithinWorkingHours() || !$member->isWorkingDay()) {
                // Find next available member
                $member = $team->teamMembers()
                    ->where('is_available', true)
                    ->where('id', '!=', $member->id)
                    ->whereRaw('current_active_leads < max_active_leads')
                    ->orderByRaw('current_active_leads / NULLIF(assignment_weight, 0) ASC')
                    ->orderBy('last_assigned_at', 'asc')
                    ->first();
            }
            
            if ($member) {
                $member->incrementActiveLeads();
                return $member->user;
            }
        }

        return null;
    }

    /**
     * Round-robin assignment among users directly mapped to a project.
     * 
     * Example: Project A has User 1, User 2, User 3
     * - Lead 1 → User 1
     * - Lead 2 → User 2
     * - Lead 3 → User 3
     * - Lead 4 → User 1 (cycle repeats)
     */
    protected function roundRobinInProject(int $projectId): ?User
    {
        // Find the next available user using round-robin order
        $userProject = UserProject::where('project_id', $projectId)
            ->where('is_available_for_leads', true)
            ->whereRaw('current_active_leads < max_active_leads')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderByRaw('current_active_leads / NULLIF(assignment_weight, 0) ASC')
            ->orderByRaw('last_lead_assigned_at ASC NULLS FIRST')
            ->first();

        if ($userProject) {
            // Update round-robin tracking
            $userProject->incrementActiveLeads();
            return $userProject->user;
        }

        return null;
    }

    protected function getUserTeam(User $user, int $projectId): ?Team
    {
        return Team::whereHas('teamMembers', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where(function ($q) use ($projectId) {
            $q->where('project_id', $projectId)->orWhereNull('project_id');
        })->active()->first();
    }

    protected function performAssignment(Lead $lead, User $assignee, ?Team $team): void
    {
        $lead->update([
            'original_owner_id' => $lead->original_owner_id ?? $assignee->id,
            'current_assignee_id' => $assignee->id,
            'team_id' => $team?->id,
            'assigned_at' => now(),
            'ownership_locked' => true,
        ]);

        $lead->logActivity(
            'assignment',
            "Lead assigned to {$assignee->full_name}" . ($team ? " in team {$team->name}" : ''),
            null,
            $assignee->id,
            ['team_id' => $team?->id, 'rule_id' => $lead->assignment_rule_id]
        );
    }

    protected function setSlaTimer(Lead $lead): void
    {
        $slaMinutes = Lead::SLA_COLD; // Default
        
        if ($lead->temperatureTag) {
            $tagSlug = $lead->temperatureTag->slug;
            if ($tagSlug === 'hot') {
                $slaMinutes = Lead::SLA_HOT;
            } elseif ($tagSlug === 'warm') {
                $slaMinutes = Lead::SLA_WARM;
            }
        }
        
        $lead->update([
            'first_call_due_at' => now()->addMinutes($slaMinutes),
        ]);
    }

    public function reassignLead(Lead $lead, User $toUser, string $reason, ?string $notes = null, bool $transferOwnership = false): bool
    {
        return DB::transaction(function () use ($lead, $toUser, $reason, $notes, $transferOwnership) {
            $fromUser = $lead->currentAssignee;
            $fromTeam = $lead->team;
            $toTeam = $this->getUserTeam($toUser, $lead->project_id);

            // Decrement old assignee's active leads
            if ($fromUser) {
                $oldMember = TeamMember::where('user_id', $fromUser->id)
                    ->where('team_id', $fromTeam?->id)
                    ->first();
                $oldMember?->decrementActiveLeads();
            }

            // Increment new assignee's active leads
            $newMember = TeamMember::where('user_id', $toUser->id)
                ->where('team_id', $toTeam?->id)
                ->first();
            $newMember?->incrementActiveLeads();

            // Update lead
            $lead->update([
                'current_assignee_id' => $toUser->id,
                'team_id' => $toTeam?->id,
                'original_owner_id' => $transferOwnership ? $toUser->id : $lead->original_owner_id,
                'ownership_transferred_at' => $transferOwnership ? now() : $lead->ownership_transferred_at,
                'ownership_transfer_reason' => $transferOwnership ? $reason : $lead->ownership_transfer_reason,
            ]);

            // Log reassignment
            $lead->reassignments()->create([
                'from_user_id' => $fromUser?->id,
                'to_user_id' => $toUser->id,
                'from_team_id' => $fromTeam?->id,
                'to_team_id' => $toTeam?->id,
                'reason' => $reason,
                'notes' => $notes,
                'reassigned_by' => auth()->id(),
                'ownership_transferred' => $transferOwnership,
            ]);

            $lead->logActivity(
                'reassignment',
                "Lead reassigned from " . ($fromUser?->full_name ?? 'Unassigned') . " to {$toUser->full_name}",
                $fromUser?->id,
                $toUser->id,
                ['reason' => $reason, 'ownership_transferred' => $transferOwnership]
            );

            return true;
        });
    }
}
