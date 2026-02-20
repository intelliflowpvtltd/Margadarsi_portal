<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisqualifyLeadRequest;
use App\Http\Requests\HandoverLeadRequest;
use App\Http\Requests\LogCallRequest;
use App\Http\Requests\MarkLostLeadRequest;
use App\Http\Requests\ScheduleFollowupRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\TransitionStatusRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Services\LeadWorkflowService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected LeadWorkflowService $workflowService;

    public function __construct(LeadWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of leads with filtering and pagination.
     * 
     * Query params:
     * - status: Filter by status
     * - stage: Filter by stage
     * - project_id: Filter by project
     * - assignee_id: Filter by assignee
     * - search: Search by name, mobile, email
     * - per_page: Items per page (default: 20)
     */
    public function index(Request $request)
    {
        $query = Lead::query()
            ->with(['project', 'currentAssignee', 'temperatureTag', 'leadSource'])
            ->visibleToCurrentUser();

        // Apply filters
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('stage')) {
            $query->withStage($request->stage);
        }

        if ($request->filled('project_id')) {
            $query->forProject($request->project_id);
        }

        if ($request->filled('assignee_id')) {
            $query->assignedTo($request->assignee_id);
        }

        if ($request->filled('team_id')) {
            $query->forTeam($request->team_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by active/closed
        if ($request->boolean('active_only')) {
            $query->active();
        } elseif ($request->boolean('closed_only')) {
            $query->closed();
        }

        // Filter by SLA breach
        if ($request->boolean('sla_breach')) {
            $query->slaBreach();
        }

        // Filter by followup due
        if ($request->boolean('followup_due')) {
            $query->followupDue();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'from' => $leads->firstItem(),
                'to' => $leads->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created lead.
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create(array_merge($request->validated(), [
            'status' => Lead::STATUS_NEW,
            'stage' => Lead::STAGE_NEW,
        ]));

        // Auto-assign lead
        $assignmentService = app(\App\Services\LeadAssignmentService::class);
        $assignee = $assignmentService->assignLead($lead);

        $lead->load(['project', 'currentAssignee', 'temperatureTag', 'leadSource']);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load([
            'project',
            'company',
            'currentAssignee',
            'originalOwner',
            'team',
            'temperatureTag',
            'leadSource',
            'budgetRange',
            'propertyType',
            'timeline',
            'closureReason',
            'nqReason',
            'calls' => fn($q) => $q->latest()->limit(10),
            'activities' => fn($q) => $q->latest()->limit(20),
            'siteVisits' => fn($q) => $q->latest(),
            'reassignments' => fn($q) => $q->latest(),
        ]);

        // Get workflow stats
        $workflowStats = $this->workflowService->getWorkflowStats($lead);

        return response()->json([
            'success' => true,
            'data' => $lead,
            'workflow' => $workflowStats,
        ]);
    }

    /**
     * Update the specified lead.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $lead->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'data' => $lead->fresh(['project', 'currentAssignee', 'temperatureTag']),
        ]);
    }

    /**
     * Soft delete the specified lead.
     */
    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }

    /**
     * Log a call for the lead.
     */
    public function logCall(LogCallRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $call = $this->workflowService->logCall($lead, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Call logged successfully',
            'data' => $call,
            'lead' => $lead->fresh(['currentAssignee']),
        ]);
    }

    /**
     * Transition lead status.
     */
    public function transitionStatus(TransitionStatusRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        try {
            $this->workflowService->transitionStatus(
                $lead,
                $request->status,
                $request->reason,
                $request->nq_reason_id,
                $request->closure_reason_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $lead->fresh(['currentAssignee']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mark lead as qualified.
     */
    public function markQualified(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        try {
            $this->workflowService->markQualified($lead, $request->input('notes'));

            return response()->json([
                'success' => true,
                'message' => 'Lead marked as qualified',
                'data' => $lead->fresh(['currentAssignee']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mark lead as not qualified.
     */
    public function markNotQualified(DisqualifyLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        try {
            $this->workflowService->markNotQualified(
                $lead,
                $request->nq_reason_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead marked as not qualified',
                'data' => $lead->fresh(['currentAssignee', 'nqReason']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Hand over lead to sales team.
     */
    public function handOver(HandoverLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        try {
            $this->workflowService->handOver($lead, $request->input('notes'));

            return response()->json([
                'success' => true,
                'message' => 'Lead handed over successfully',
                'data' => $lead->fresh(['currentAssignee', 'handedOverBy']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mark lead as lost.
     */
    public function markLost(MarkLostLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        try {
            $this->workflowService->markLost(
                $lead,
                $request->closure_reason_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead marked as lost',
                'data' => $lead->fresh(['currentAssignee', 'closureReason']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Schedule a follow-up.
     */
    public function scheduleFollowup(ScheduleFollowupRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $this->workflowService->scheduleFollowup(
            $lead,
            new \DateTime($request->followup_at),
            $request->notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => $lead->fresh(),
        ]);
    }

    /**
     * Get lead statistics grouped by status.
     */
    public function statistics(Request $request)
    {
        $query = Lead::query()->visibleToCurrentUser();

        // Apply project filter if provided
        if ($request->filled('project_id')) {
            $query->forProject($request->project_id);
        }

        $stats = [
            'by_status' => [],
            'by_stage' => [],
            'total' => $query->count(),
            'active' => (clone $query)->active()->count(),
            'closed' => (clone $query)->closed()->count(),
            'sla_breached' => (clone $query)->slaBreach()->count(),
            'followups_due' => (clone $query)->followupDue()->count(),
            'dormant' => (clone $query)->dormant()->count(),
        ];

        // Count by status
        foreach (Lead::STATUSES as $status => $config) {
            $stats['by_status'][$status] = (clone $query)->withStatus($status)->count();
        }

        // Count by stage
        foreach (Lead::STAGES as $stage => $config) {
            $stats['by_stage'][$stage] = (clone $query)->withStage($stage)->count();
        }

        // Average SLA response time (only for leads that have responded)
        $avgResponse = (clone $query)->whereNotNull('sla_response_seconds')->avg('sla_response_seconds');
        $stats['avg_response_seconds'] = $avgResponse ? round($avgResponse) : null;

        // Today's metrics
        $today = now()->startOfDay();
        $stats['today_new'] = (clone $query)->where('created_at', '>=', $today)->count();
        $stats['today_calls'] = \App\Models\LeadCall::whereHas('lead', function ($q) use ($query) {
            $q->whereIn('id', (clone $query)->select('id'));
        })->where('created_at', '>=', $today)->count();
        $stats['today_qualified'] = (clone $query)->withStatus('qualified')
            ->where('updated_at', '>=', $today)->count();
        $stats['today_handed_over'] = (clone $query)->withStatus('handed_over')
            ->where('handed_over_at', '>=', $today)->count();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
