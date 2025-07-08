<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feed;
use App\Http\Resources\FeedResource;

class FeedController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'command' => 'required|in:ON,OFF'
        ]);

        $last = Feed::orderByDesc('created_at')->first();

        if ($last && $last->status === $request->command) {
            if($last->status == "ON"){
                return response()->json([
                    'message' => 'Perintah tidak disimpan. Pakan otomatis sudah dalam keadaan aktif!',
                    'status' => 409,
                    'data' => new FeedResource($last)
                ], 409);
            }elseif($last->status == "OFF"){
                return response()->json([
                    'message' => 'Perintah tidak disimpan. Pakan otomatis sudah dalam keadaan nonaktif!',
                    'status' => 409,
                    'data' => new FeedResource($last)
                ], 409);
            }
        }

        $feed = Feed::create([
            'device_id' => $request->device_id,
            'status' => $request->command
        ]);

        if($request->command == "ON"){
            return response()->json([
                'message' => 'Pakan otomatis berhasil dinyalakan.',
                'status' => 201,
                'data' => new FeedResource($feed)
            ], 201);
        }elseif($request->command == "OFF"){
            return response()->json([
                'message' => 'Pakan otomatis berhasil dimatikan.',
                'status' => 201,
                'data' => new FeedResource($feed)
            ], 201);
        }
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
        $historyData = Feed::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Histori feed berhasil dimuat.',
            'status' => 200,
            'data' => FeedResource::collection($historyData)
        ], 200);
    }
}
