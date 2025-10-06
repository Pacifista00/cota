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

