<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeedSchedule;
use App\Http\Resources\FeedScheduleResource;

class FeedScheduleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'waktu_pakan' => 'required',
        ]);

        try {
            $jadwal = FeedSchedule::create([
                'waktu_pakan' => $request->waktu_pakan
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil disimpan!',
                    'status' => 201,
                    'data' => new FeedScheduleResource($jadwal)
                ], 201);
            } else {
                return redirect()->back()->with('success', 'Jadwal pakan berhasil disimpan!');
            }

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menyimpan jadwal pakan.',
                    'status' => 500
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal pakan.');
            }
        }
    }

    public function update(Request $request, $id){
        $request->validate([
            'waktu_pakan' => 'required',
        ]);

        $jadwal = FeedSchedule::findOrFail($id);

        try {
            $jadwal->update([
                'waktu_pakan' => $request->waktu_pakan
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil diubah!',
                    'status' => 200,
                    'data' => new FeedScheduleResource($jadwal)
                ], 200);
            } else {
                return redirect()->back()->with('success', 'Jadwal pakan berhasil diubah!');
            }

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menyimpan jadwal pakan.',
                    'status' => 500
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah jadwal pakan.');
            }
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $jadwal = FeedSchedule::findOrFail($id);
            $jadwal->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Jadwal pakan berhasil dihapus!',
                    'status' => 200
                ], 200);
            } else {
                return redirect()->back()->with('success', 'Jadwal pakan berhasil dihapus!');
            }

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menghapus jadwal pakan.',
                    'status' => 500
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus jadwal pakan.');
            }
        }
    }

}
