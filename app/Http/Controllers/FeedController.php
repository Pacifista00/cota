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
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class FeedController extends Controller
{
    public function beriPakan(Request $request)
    {
        // --- Konfigurasi MQTT (Sudah Diupdate) ---
        $server   = 'chameleon.lmq.cloudamqp.com';
        $port     = 8883;
        $clientId = 'laravel-client-' . rand();
        $username = 'anfvrqjy:anfvrqjy';
        $password = 'V4OJdwnNv8d8nN2OmCbLrdBqDF5-WS5G';

        // GANTI topik dinamis menjadi topik statis/broadcast
        $topic    = 'cota/command/feed_all';

        try {
            // Pengaturan koneksi untuk menggunakan TLS (koneksi aman)
            $connectionSettings = (new ConnectionSettings)
                ->setUseTls(true)
                ->setTlsSelfSignedAllowed(true)
                ->setUsername($username)
                ->setPassword($password);

            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, 'FEED', MqttClient::QOS_AT_LEAST_ONCE);
            $mqtt->disconnect();

            // Simpan record ke FeedExecution untuk tracking
            FeedExecution::create([
                'status' => 'pending',
                'executed_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Perintah pakan broadcast telah dikirim!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke MQTT Broker: ' . $e->getMessage()], 500);
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
