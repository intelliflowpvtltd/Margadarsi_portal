<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use App\Services\LeadScopeService;
use Illuminate\Auth\Access\Response;

class LeadPolicy
{
    protected LeadScopeService $scopeService;

    public function __construct(LeadScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    /**
     * Determine whether the user can view any models.
     * All authenticated users can list leads (filtered by their visibility).
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasPermission('leads.view');
    }

    /**
     * Determine whether the user can view the model.
     * Uses LeadScopeService to check role-based visibility.
     */
    public function view(User $user, Lead $lead): bool
    {
        if (!$user->is_active || !$user->hasPermission('leads.view')) {
            return false;
        }
        return $this->scopeService->canView($user, $lead);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active && $user->hasPermission('leads.create');
    }

    /**
     * Determine whether the user can update the model.
     * Uses LeadScopeService to check role-based management permission.
     */
    public function update(User $user, Lead $lead): bool
    {
        if (!$user->is_active || !$user->hasPermission('leads.update')) {
            return false;
        }
        return $this->scopeService->canManage($user, $lead);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        if (!$user->is_active || !$user->hasPermission('leads.delete')) {
            return false;
        }
        return $this->scopeService->canManage($user, $lead);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lead $lead): bool
    {
        if (!$user->is_active || !$user->hasPermission('leads.delete')) {
            return false;
        }
        return $this->scopeService->canManage($user, $lead);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->is_active && $user->hasPermission('leads.force_delete');
    }

    /**
     * Determine whether the user can reassign the lead.
     */
    public function reassign(User $user, Lead $lead): bool
    {
        if (!$user->is_active || !$user->hasPermission('leads.update')) {
            return false;
        }

        // Telecallers cannot reassign
        if ($user->isTelecaller()) {
            return false;
        }

        // Team Lead can reassign within their team
        if ($user->isTeamLead()) {
            $teamIds = $user->getAccessibleTeamIds();
            return in_array($lead->team_id, $teamIds);
        }

        // Project Manager, Sales Manager, Admin - can reassign if they can manage
        return $this->scopeService->canManage($user, $lead);
    }

    /**
     * Determine whether the user can transfer ownership of the lead.
     * This is a privileged action requiring manager approval.
     */
    public function transferOwnership(User $user, Lead $lead): bool
    {
        if (!$user->is_active) {
            return false;
        }

        // Only managers (level <= 4) can transfer ownership
        return $user->role && $user->role->hierarchy_level <= 4;
    }
}
