<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feed;
use App\Models\FeedSchedule;
use App\Models\FeedExecution;
use App\Http\Resources\FeedResource;
use App\Http\Resources\FeedExecutionResource;
use App\Services\FeedSchedulingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class FeedController extends Controller
{
    protected FeedSchedulingService $feedSchedulingService;

    public function __construct(FeedSchedulingService $feedSchedulingService)
    {
        $this->feedSchedulingService = $feedSchedulingService;
    }

    /**
     * Beri pakan manual (tidak terkait jadwal)
     */
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

    public function history(Request $request)
    {
        $historyData = FeedExecution::orderBy('executed_at', 'desc')->get();

        return response()->json([
            'message' => 'Histori feed berhasil dimuat.',
            'status' => 200,
            'data' => FeedExecutionResource::collection($historyData)
        ], 200);
    }

    public function checkFeedStatus(Request $request)
    {
        // Cek eksekusi terbaru yang masih pending
        $lastExecution = FeedExecution::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(5)) // Hanya cek dalam 5 menit terakhir
            ->latest()
            ->first();
        
        if ($lastExecution) {
            // Simulasi: setelah 3 detik, anggap berhasil
            if ($lastExecution->created_at->diffInSeconds(now()) >= 3) {
                // Update status menjadi success
                $lastExecution->update(['status' => 'success']);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pakan berhasil diberikan!',
                    'executed_at' => $lastExecution->executed_at
                        ? $lastExecution->executed_at->timezone('Asia/Jakarta')->toIso8601String()
                        : null
                ]);
            } else {
                return response()->json([
                    'status' => 'pending',
                    'message' => 'Menunggu konfirmasi dari device...'
                ]);
            }
        }
        
        return response()->json([
            'status' => 'pending',
            'message' => 'Tidak ada perintah pakan yang sedang diproses'
        ]);
    }
}