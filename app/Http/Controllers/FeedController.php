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
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("http://192.168.18.89/beri-pakan");

            if ($response->getStatusCode() === 200) {
                FeedExecution::create([
                    'status' => 'success',
                    'executed_at' => now(),
                ]);
                return redirect()->back()->with('success', 'Pakan berhasil diberikan!');
            }
            FeedExecution::create([
                'status' => 'failed',
                'executed_at' => now(),
            ]);
            return redirect()->back()->with('error', 'Gagal menembak API! (Status: ' . $response->getStatusCode() . ')');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function beriPakanTerjadwal(Request $request, $id)
    {
        $jadwal = FeedSchedule::find($id);

        if (!$jadwal) {
            return response()->json([
                'message' => 'Jadwal tidak ditemukan.',
                'status'  => 'error'
            ], 404);
        }

        $today = Carbon::today()->toDateString(); // gunakan Carbon

        // Cek apakah sudah dieksekusi hari ini
        if ($jadwal->last_executed_at === $today) {
            return response()->json([
                'message' => 'Jadwal ini sudah dieksekusi hari ini.',
                'status'  => 'info'
            ], 200);
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("http://192.168.18.89/beri-pakan");

            if ($response->getStatusCode() === 200) {
                // Update jadwal terakhir dieksekusi
                $jadwal->update([
                    'last_executed_at' => $today,
                ]);

                feedExecution::create([
                    'status' => 'success',
                    'executed_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Pakan berhasil diberikan!',
                    'status'  => 'success'
                ], 200);
            }

            return response()->json([
                'message' => 'Gagal menembak API! (Status: ' . $response->getStatusCode() . ')',
                'status'  => 'error'
            ], $response->getStatusCode());
        } catch (\Exception $e) {
            FeedExecution::create([
                'status' => 'failed',
                'executed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
                'status'  => 'error'
            ], 500);
        }
    }

    public function siap()
    {
        $now = Carbon::now();
        $satuMenitLalu = $now->copy()->subMinute();
        $tanggalHariIni = $now->toDateString();

        $jadwals = FeedSchedule::whereTime('waktu_pakan', '<=', $now->format('H:i:s'))
            ->whereTime('waktu_pakan', '>=', $satuMenitLalu->format('H:i:s'))
            ->where(function ($q) use ($tanggalHariIni) {
                $q->whereNull('last_executed_at')
                    ->orWhereDate('last_executed_at', '<>', $tanggalHariIni);
            })
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
