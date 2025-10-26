<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedScheduleRequest;
use App\Http\Requests\UpdateFeedScheduleRequest;
use App\Http\Resources\FeedScheduleResource;
use App\Models\FeedSchedule;
use App\Services\FeedSchedulingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FeedScheduleController extends Controller
{
    protected FeedSchedulingService $feedSchedulingService;

    public function __construct(FeedSchedulingService $feedSchedulingService)
    {
        $this->feedSchedulingService = $feedSchedulingService;
    }

    /**
     * Display a listing of feed schedules
     */
    #[OA\Get(
        path: '/feed-schedule',
        summary: 'List Feed Schedules',
        description: 'Get a list of all feed schedules for the authenticated user, including recent execution history',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedules retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleListResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? null;
        
        $schedules = FeedSchedule::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->with(['executions' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar jadwal pakan berhasil dimuat.',
            'status' => 200,
            'data' => FeedScheduleResource::collection($schedules),
        ], 200);
    }

    /**
     * Display the specified feed schedule
     */
    #[OA\Get(
        path: '/feed-schedule/{id}',
        summary: 'Get Feed Schedule Details',
        description: 'Get detailed information about a specific feed schedule including statistics and execution history',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedule details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Detail jadwal pakan berhasil dimuat.'),
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(
                            property: 'statistics',
                            properties: [
                                new OA\Property(property: 'total_executions', type: 'integer', example: 30),
                                new OA\Property(property: 'success_rate', type: 'number', example: 96.7),
                                new OA\Property(property: 'average_delay', type: 'number', example: 2.5),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Feed schedule not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function show(Request $request, $id): JsonResponse
    {
        $schedule = FeedSchedule::with('executions')->findOrFail($id);

        // Get statistics
        $statistics = $this->feedSchedulingService->getScheduleStatistics($schedule);

        return response()->json([
            'message' => 'Detail jadwal pakan berhasil dimuat.',
            'status' => 200,
            'data' => new FeedScheduleResource($schedule),
            'statistics' => $statistics,
        ], 200);
    }

    /**
     * Store a newly created feed schedule
     */
    #[OA\Post(
        path: '/feed-schedule/create',
        summary: 'Create Feed Schedule',
        description: 'Create a new automated feeding schedule with customizable frequency and time settings',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Feed schedule data',
            content: new OA\JsonContent(ref: '#/components/schemas/CreateFeedScheduleRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Feed schedule created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function store(StoreFeedScheduleRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $schedule = $this->feedSchedulingService->createSchedule($request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil disimpan!',
                    'status' => 201,
                    'data' => new FeedScheduleResource($schedule),
                ], 201);
            }

            return redirect()->back()->with('success', 'Jadwal pakan berhasil disimpan!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menyimpan jadwal pakan: ' . $e->getMessage(),
                    'status' => 500,
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal pakan.');
        }
    }

    /**
     * Update the specified feed schedule
     */
    #[OA\Put(
        path: '/feed-schedule/{id}',
        summary: 'Update Feed Schedule',
        description: 'Update an existing feed schedule with new settings (all fields are optional)',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Updated feed schedule data',
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateFeedScheduleRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedule updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Feed schedule not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function update(UpdateFeedScheduleRequest $request, $id): JsonResponse|RedirectResponse
    {
        $schedule = FeedSchedule::findOrFail($id);

        try {
            $schedule = $this->feedSchedulingService->updateSchedule($schedule, $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil diubah!',
                    'status' => 200,
                    'data' => new FeedScheduleResource($schedule),
                ], 200);
            }

            return redirect()->back()->with('success', 'Jadwal pakan berhasil diubah!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat mengubah jadwal pakan: ' . $e->getMessage(),
                    'status' => 500,
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah jadwal pakan.');
        }
    }

    /**
     * Remove the specified feed schedule
     */
    #[OA\Delete(
        path: '/feed-schedule/{id}',
        summary: 'Delete Feed Schedule',
        description: 'Delete an existing feed schedule permanently',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedule deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Jadwal pakan berhasil dihapus!'),
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Feed schedule not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $schedule = FeedSchedule::findOrFail($id);
            $this->feedSchedulingService->deleteSchedule($schedule);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil dihapus!',
                    'status' => 200,
                ], 200);
            }

            return redirect()->back()->with('success', 'Jadwal pakan berhasil dihapus!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menghapus jadwal pakan: ' . $e->getMessage(),
                    'status' => 500,
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus jadwal pakan.');
        }
    }

    /**
     * Activate a feed schedule
     */
    #[OA\Patch(
        path: '/feed-schedule/{id}/activate',
        summary: 'Activate Feed Schedule',
        description: 'Activate a feed schedule to enable automatic feeding',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedule activated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Feed schedule not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function activate(Request $request, $id): JsonResponse
    {
        $schedule = FeedSchedule::findOrFail($id);

        try {
            $schedule = $this->feedSchedulingService->activateSchedule($schedule);

            return response()->json([
                'message' => 'Jadwal pakan berhasil diaktifkan!',
                'status' => 200,
                'data' => new FeedScheduleResource($schedule),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengaktifkan jadwal pakan: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Deactivate a feed schedule
     */
    #[OA\Patch(
        path: '/feed-schedule/{id}/deactivate',
        summary: 'Deactivate Feed Schedule',
        description: 'Deactivate a feed schedule to disable automatic feeding',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed schedule deactivated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Feed schedule not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function deactivate(Request $request, $id): JsonResponse
    {
        $schedule = FeedSchedule::findOrFail($id);

        try {
            $schedule = $this->feedSchedulingService->deactivateSchedule($schedule);

            return response()->json([
                'message' => 'Jadwal pakan berhasil dinonaktifkan!',
                'status' => 200,
                'data' => new FeedScheduleResource($schedule),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menonaktifkan jadwal pakan: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Get active schedules for authenticated user
     */
    #[OA\Get(
        path: '/feed-schedule/active',
        summary: 'Get Active Feed Schedules',
        description: 'Get all currently active feed schedules for the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Feed Schedule'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active feed schedules retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedScheduleListResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function active(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? null;
        $schedules = $this->feedSchedulingService->getUserActiveSchedules($userId);

        return response()->json([
            'message' => 'Jadwal pakan aktif berhasil dimuat.',
            'status' => 200,
            'data' => FeedScheduleResource::collection($schedules),
        ], 200);
    }
}

