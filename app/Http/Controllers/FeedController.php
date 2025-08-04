<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feed;
use App\Models\FeedSchedule;
use App\Models\FeedExecution;
use App\Http\Resources\FeedResource;
use App\Http\Resources\FeedExecutionResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class FeedController extends Controller
{
    public function beriPakan()
    {
        $response = Http::get('http://192.168.18.34/beri-pakan');

        if ($response->successful()) {
            return response()->json([
                'message' => 'Pakan berhasil diberikan!',
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal menembak API!',
            'status' => 500
        ], 500);
    }
    public function beriPakanTerjadwal(Request $request, $id)
    {
        $jadwal = FeedSchedule::find($id);

        if (!$jadwal) {
            return response()->json([
                'message' => 'Jadwal tidak ditemukan.',
                'status' => 404
            ], 404);
        }

        // Cek apakah jadwal sudah dieksekusi hari ini
        $sudahAda = $jadwal->executions()
            ->whereDate('executed_at', Carbon::today())
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'message' => 'Jadwal sudah dieksekusi hari ini.',
                'status' => 200
            ], 200);
        }

        // Coba tembak API pakan
        $response = Http::get('http://192.168.18.34/beri-pakan');

        if ($response->successful()) {
            // Simpan eksekusi jika sukses
            FeedExecution::create([
                'feed_schedule_id' => $jadwal->id,
                'status' => 'success',
                'executed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Pakan berhasil diberikan!',
                'status' => 200,
            ], 200);
        } else {
            // Simpan eksekusi jika gagal
            FeedExecution::create([
                'feed_schedule_id' => $jadwal->id,
                'status' => 'failed',
                'executed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Pakan gagal diberikan!',
                'status' => 500,
            ], 500);
        }

        return response()->json([
            'message' => 'Pakan gagal diberikan!',
            'status' => 500,
        ], 500);
    }

    public function siap()
    {
        $now = Carbon::now(); // waktu sekarang
        $satuMenitLalu = $now->copy()->subMinute(); // waktu 1 menit yang lalu
        $tanggalHariIni = $now->toDateString();

        // Format jadi H:i:s
        $nowFormatted = $now->format('H:i:s');
        $satuMenitLaluFormatted = $satuMenitLalu->format('H:i:s');

        // Format jadi His
        $nowHis = $now->format('His');
        $satuMenitLaluHis = $satuMenitLalu->format('His');

        $jadwals = FeedSchedule::whereTime('waktu_pakan', '<=', $now->format('H:i:s'))
        ->whereTime('waktu_pakan', '>=', $satuMenitLalu->format('H:i:s'))
        ->whereDate('created_at', $tanggalHariIni)
        ->get();

        return response()->json($jadwals);
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
}
