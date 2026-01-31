<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Lead Scope Service
 * 
 * Applies role-based data filtering for leads based on user's role hierarchy.
 * 
 * VISIBILITY RULES:
 * - Telecaller: Only own assigned leads (current_assignee_id = user_id)
 * - Team Lead: All leads in their team(s) (team_id IN user's teams)
 * - Project Manager: All leads for managed projects (project_id IN user's projects)
 * - Sales Manager: All leads in assigned teams + subordinates' leads
 * - Sales Director/Admin/Super Admin: All company leads
 * - Channel Partner: Only leads they submitted (cp_user_id = user_id)
 */
class LeadScopeService
{
    /**
     * Apply visibility scope to lead query based on user's role.
     */
    public function applyScope(Builder $query, User $user): Builder
    {
        // Always filter by company first
        $query->where('leads.company_id', $user->company_id);

        // Super Admin, Admin, Sales Director - see all company leads
        if ($user->hasCompanyWideAccess()) {
            return $query;
        }

        // Channel Partner - only see leads they submitted
        if ($user->isChannelPartner()) {
            return $query->where('leads.cp_user_id', $user->id);
        }

        // Sales Manager - see leads in their teams + subordinates' leads
        if ($user->isSalesManager()) {
            return $this->applySalesManagerScope($query, $user);
        }

        // Project Manager - see all leads in their managed projects
        if ($user->isProjectManager()) {
            return $this->applyProjectManagerScope($query, $user);
        }

        // Team Lead - see all leads in their team(s)
        if ($user->isTeamLead()) {
            return $this->applyTeamLeadScope($query, $user);
        }

        // Telecaller (default) - only see own assigned leads
        return $this->applyTelecallerScope($query, $user);
    }

    /**
     * Telecaller: Only own assigned leads.
     */
    protected function applyTelecallerScope(Builder $query, User $user): Builder
    {
        return $query->where('leads.current_assignee_id', $user->id);
    }

    /**
     * Team Lead: All leads in their team(s).
     */
    protected function applyTeamLeadScope(Builder $query, User $user): Builder
    {
        $teamIds = $user->getAccessibleTeamIds();

        if (empty($teamIds)) {
            // No teams - fallback to own leads only
            return $query->where('leads.current_assignee_id', $user->id);
        }

        return $query->where(function ($q) use ($user, $teamIds) {
            $q->whereIn('leads.team_id', $teamIds)
              ->orWhere('leads.current_assignee_id', $user->id);
        });
    }

    /**
     * Project Manager: All leads in their managed projects.
     */
    protected function applyProjectManagerScope(Builder $query, User $user): Builder
    {
        $projectIds = $user->getAccessibleProjectIds();

        if (empty($projectIds)) {
            // No projects - fallback to own leads only
            return $query->where('leads.current_assignee_id', $user->id);
        }

        return $query->whereIn('leads.project_id', $projectIds);
    }

    /**
     * Sales Manager: Leads in their teams + subordinates' leads.
     */
    protected function applySalesManagerScope(Builder $query, User $user): Builder
    {
        $teamIds = $user->getAccessibleTeamIds();
        $subordinateIds = $user->getSubordinateIds();
        $projectIds = $user->getAccessibleProjectIds();

        return $query->where(function ($q) use ($user, $teamIds, $subordinateIds, $projectIds) {
            // Own leads
            $q->where('leads.current_assignee_id', $user->id);

            // Leads in their teams
            if (!empty($teamIds)) {
                $q->orWhereIn('leads.team_id', $teamIds);
            }

            // Subordinates' leads
            if (!empty($subordinateIds)) {
                $q->orWhereIn('leads.current_assignee_id', $subordinateIds);
            }

            // Leads in their projects
            if (!empty($projectIds)) {
                $q->orWhereIn('leads.project_id', $projectIds);
            }
        });
    }

    /**
     * Check if user can view a specific lead.
     */
    public function canView(User $user, Lead $lead): bool
    {
        // Must be same company
        if ($user->company_id !== $lead->company_id) {
            return false;
        }

        // Company-wide access
        if ($user->hasCompanyWideAccess()) {
            return true;
        }

        // Channel Partner - only their submitted leads
        if ($user->isChannelPartner()) {
            return $lead->cp_user_id === $user->id;
        }

        // Own assigned lead
        if ($lead->current_assignee_id === $user->id) {
            return true;
        }

        // Original owner
        if ($lead->original_owner_id === $user->id) {
            return true;
        }

        // Team Lead - leads in their teams
        if ($user->isTeamLead()) {
            $teamIds = $user->getAccessibleTeamIds();
            return in_array($lead->team_id, $teamIds);
        }

        // Project Manager - leads in their projects
        if ($user->isProjectManager()) {
            $projectIds = $user->getAccessibleProjectIds();
            return in_array($lead->project_id, $projectIds);
        }

        // Sales Manager - teams, subordinates, or projects
        if ($user->isSalesManager()) {
            $teamIds = $user->getAccessibleTeamIds();
            $subordinateIds = $user->getSubordinateIds();
            $projectIds = $user->getAccessibleProjectIds();

            return in_array($lead->team_id, $teamIds) ||
                   in_array($lead->current_assignee_id, $subordinateIds) ||
                   in_array($lead->project_id, $projectIds);
        }

        return false;
    }

    /**
     * Check if user can manage (edit/update) a specific lead.
     */
    public function canManage(User $user, Lead $lead): bool
    {
        // Must be able to view first
        if (!$this->canView($user, $lead)) {
            return false;
        }

        // Company-wide access can manage all
        if ($user->hasCompanyWideAccess()) {
            return true;
        }

        // Assigned user can manage
        if ($lead->current_assignee_id === $user->id) {
            return true;
        }

        // Team Lead can manage team leads
        if ($user->isTeamLead()) {
            $teamIds = $user->getAccessibleTeamIds();
            return in_array($lead->team_id, $teamIds);
        }

        // Project Manager can manage project leads
        if ($user->isProjectManager()) {
            $projectIds = $user->getAccessibleProjectIds();
            return in_array($lead->project_id, $projectIds);
        }

        // Sales Manager can manage
        if ($user->isSalesManager()) {
            return true;
        }

        return false;
    }

    /**
     * Get visibility description for user's role.
     */
    public function getVisibilityDescription(User $user): string
    {
        if ($user->hasCompanyWideAccess()) {
            return 'All company leads';
        }

        if ($user->isChannelPartner()) {
            return 'Only leads you submitted';
        }

        if ($user->isSalesManager()) {
            return 'Leads in your teams, subordinates, and assigned projects';
        }

        if ($user->isProjectManager()) {
            $projects = $user->projects()->pluck('name')->join(', ');
            return "All leads in projects: {$projects}";
        }

        if ($user->isTeamLead()) {
            $teams = $user->teams()->pluck('name')->join(', ');
            return "All leads in teams: {$teams}";
        }

        return 'Only your assigned leads';
    }
}
