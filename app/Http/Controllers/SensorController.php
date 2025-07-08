<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Http\Resources\SensorResource;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kekeruhan' => 'required|numeric',
            'keasaman' => 'required|numeric',
            'suhu' => 'required|numeric',
        ]);

        try {
            $sensorData = Sensor::create($validatedData);

            return response()->json([
                'message' => 'Data sensor berhasil ditambahkan!',
                'status' => 201,
                'data' => new SensorResource($sensorData)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan data sensor.',
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function latest()
    {
        $latestSensorData = Sensor::latest()->first();

        if (!$latestSensorData) {
            return response()->json([
                'message' => 'Data sensor tidak ditemukan.',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Data sensor terbaru berhasil dimuat.',
            'status' => 200,
            'data' => new SensorResource($latestSensorData)
        ], 200);
    }
    public function history(Request $request)
    {
        $historyData = Sensor::orderBy('created_at', 'desc')->get();

        if ($historyData->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data history.',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Data history sensor berhasil dimuat.',
            'status' => 200,
            'data' => SensorResource::collection($historyData)
        ], 200);
    }
}
