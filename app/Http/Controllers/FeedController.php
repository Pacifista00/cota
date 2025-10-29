<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\FeedExecutionStatus;
use App\Models\Feed;
use App\Models\FeedSchedule;
use App\Models\FeedExecution;
use App\Http\Resources\FeedResource;
use App\Http\Resources\FeedExecutionResource;
use App\Services\FeedSchedulingService;
use App\Services\FeedStatusUpdaterService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class FeedController extends Controller
{
    protected FeedSchedulingService $feedSchedulingService;
    protected FeedStatusUpdaterService $statusUpdater;

    public function __construct(
        FeedSchedulingService $feedSchedulingService,
        FeedStatusUpdaterService $statusUpdater
    ) {
        $this->feedSchedulingService = $feedSchedulingService;
        $this->statusUpdater = $statusUpdater;
    }

    /**
     * Beri pakan manual (tidak terkait jadwal)
     */
    #[OA\Get(
        path: '/feed/give',
        summary: 'Give Manual Feed',
        description: 'Trigger manual feeding (not related to any schedule)',
        security: [['sanctum' => []]],
        tags: ['Feed'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Manual feed executed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Pakan berhasil diberikan secara manual'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Failed to execute feed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Gagal memberikan pakan'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function beriPakan(Request $request)
    {
        // Gunakan service untuk execute manual feed
        $result = $this->feedSchedulingService->executeManualFeed();

        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => $result['execution']
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message']
        ], 500);
    }

    /**
     * Beri pakan terjadwal (trigger manual dari frontend untuk jadwal tertentu)
     */
    #[OA\Get(
        path: '/feed/give/{id}',
        summary: 'Give Scheduled Feed',
        description: 'Trigger manual execution of a specific feed schedule',
        security: [['sanctum' => []]],
        tags: ['Feed'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Feed Schedule ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Scheduled feed executed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Pakan berhasil diberikan'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', type: 'object', nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Schedule not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Jadwal tidak ditemukan.'),
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Failed to execute feed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Gagal memberikan pakan'),
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function beriPakanTerjadwal(Request $request, $id)
    {
        $jadwal = FeedSchedule::find($id);

        if (!$jadwal) {
            return response()->json([
                'message' => 'Jadwal tidak ditemukan.',
                'status'  => 'error'
            ], 404);
        }

        // Gunakan service untuk execute feed dengan schedule_id
        $result = $this->feedSchedulingService->executeFeed($jadwal);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'status'  => 'success',
                'data' => $result['execution'] ?? null
            ], 200);
        }

        return response()->json([
            'message' => $result['message'],
            'status'  => 'error'
        ], 500);
    }

    /**
     * Get schedules yang siap dieksekusi (dalam 1 menit terakhir)
     */
    #[OA\Get(
        path: '/feed/ready',
        summary: 'Get Ready Feed Schedules',
        description: 'Get all feed schedules that are ready to execute (within the last minute)',
        security: [['sanctum' => []]],
        tags: ['Feed'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ready schedules retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'ready_schedules',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        ),
                        new OA\Property(property: 'count', type: 'integer', example: 2),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function siap()
    {
        $readySchedules = $this->feedSchedulingService->getReadySchedules();
        return response()->json($readySchedules);
    }

    public function status(Request $request)
    {
        $lastStatus = Feed::latest()->first();

        if (!$lastStatus) {
            return response()->json([
                'message' => 'Tidak ada data feed.',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil memuat status pakan otomatis saat ini.',
            'status' => 200,
            'data' => new FeedResource($lastStatus)
        ]);
    }

    #[OA\Get(
        path: '/feed/history',
        summary: 'Get Feed Execution History',
        description: 'Retrieve complete history of all feed executions including scheduled and manual feeds, ordered by execution time',
        security: [['sanctum' => []]],
        tags: ['Feed'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed history retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/FeedHistoryResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function history(Request $request)
    {
        $historyData = FeedExecution::orderBy('executed_at', 'desc')->get();

        return response()->json([
            'message' => 'Histori feed berhasil dimuat.',
            'status' => 200,
            'data' => FeedExecutionResource::collection($historyData)
        ], 200);
    }

    #[OA\Get(
        path: '/feed/status',
        summary: 'Check Feed Execution Status',
        description: 'Check the status of recent feed execution (for polling)',
        security: [['sanctum' => []]],
        tags: ['Feed'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed execution status retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', enum: ['success', 'pending'], example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Pakan berhasil diberikan!'),
                        new OA\Property(property: 'executed_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-15T08:00:00+07:00'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function checkFeedStatus(Request $request)
    {
        // Cek eksekusi terbaru yang masih pending
        $lastExecution = FeedExecution::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(5)) // Hanya cek dalam 5 menit terakhir
            ->latest()
            ->first();

        if ($lastExecution) {
            // Check if execution should be updated using service
            if ($this->statusUpdater->shouldUpdateExecution($lastExecution)) {
                // Update status using service
                $success = $this->statusUpdater->updateExecutionStatus(
                    $lastExecution,
                    FeedExecutionStatus::SUCCESS,
                    [
                        'update_source' => 'ui_polling',
                        'endpoint' => 'checkFeedStatus',
                    ]
                );

                if ($success) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Pakan berhasil diberikan!',
                        'executed_at' => $lastExecution->executed_at
                            ? $lastExecution->executed_at->timezone('Asia/Jakarta')->toIso8601String()
                            : null
                    ]);
                }
            }

            return response()->json([
                'status' => 'pending',
                'message' => 'Menunggu konfirmasi dari device...'
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Tidak ada perintah pakan yang sedang diproses'
        ]);
    }
}