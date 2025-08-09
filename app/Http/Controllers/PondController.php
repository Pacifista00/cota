<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pond;
use App\Http\Resources\PondResource;
use Illuminate\Support\Str;

class PondController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'lokasi' => 'required',
        ]);

        try {
            $pond = Pond::create([
                'user_id' => Auth::id(),
                'nama' => $request->nama,
                'lokasi' => $request->lokasi,
                'token_tambak' => Str::random(16),
                'status_koneksi' => 'pending',
                'status_perangkat' => 'off',
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Tambak berhasil disimpan!',
                    'status' => 201,
                    'data' => new PondResource($pond)
                ], 201);
            } else {
                return redirect()->back()->with([
                    'success' => 'Tambak berhasil disimpan!',
                    'token_tambak' => $pond->token_tambak
                ]);
            }

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menambah Tambak.',
                    'status' => 500
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menambah Tambak.');
            }
        }
    }

    public function update(Request $request, $id){
        $pond = Pond::findOrFail($id);
        $userId = Auth::id();

        if($pond->user_id != $userId){
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Anda bukan pemilik tambak!',
                    'status' => 403,
                ], 403);
            } else {
                return redirect()->back()->with('error', 'Anda bukan pemilik tambak!');
            }
        }else{
            $request->validate([
                'nama' => 'required',
                'lokasi' => 'required',
            ]);

            try {
                $pond->update([
                    'nama' => $request->nama,
                    'lokasi' => $request->lokasi
                ]);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Tambak berhasil diubah!',
                        'status' => 200,
                        'data' => new PondResource($pond)
                    ], 200);
                } else {
                    return redirect()->back()->with('success', 'Tambak berhasil diubah!');
                }

            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Terjadi kesalahan saat mengubah Tambak!',
                        'status' => 500
                    ], 500);
                } else {
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah Tambak!');
                }
            }
        }
    }
    public function destroy(Request $request, $id)
    {
        $pond = Pond::findOrFail($id);
        $userId = Auth::id();

        if($pond->user_id != $userId){
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Anda bukan pemilik tambak!',
                    'status' => 403,
                ], 403);
            } else {
                return redirect()->back()->with('error', 'Anda bukan pemilik tambak!');
            }
        }else{
            try {
                $pond = Pond::findOrFail($id);
                $pond->delete();

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Tambak berhasil dihapus!',
                        'status' => 200
                    ], 200);
                } else {
                    return redirect()->back()->with('success', 'Tambak berhasil dihapus!');
                }

            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Terjadi kesalahan saat menghapus Tambak.',
                        'status' => 500
                    ], 500);
                } else {
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus Tambak.');
                }
            }
        }
    }
}
