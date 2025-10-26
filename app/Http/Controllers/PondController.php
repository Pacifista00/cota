<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pond;
use App\Http\Resources\PondResource;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class PondController extends Controller
{
    #[OA\Post(
        path: '/pond/store',
        summary: 'Create Pond',
        description: 'Create a new fish pond with auto-generated token',
        security: [['sanctum' => []]],
        tags: ['Pond'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Pond data',
            content: new OA\JsonContent(ref: '#/components/schemas/CreatePondRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pond created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/PondResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
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

    #[OA\Put(
        path: '/pond/update/{id}',
        summary: 'Update Pond',
        description: 'Update pond information (only owner can update)',
        security: [['sanctum' => []]],
        tags: ['Pond'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Pond ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Updated pond data',
            content: new OA\JsonContent(ref: '#/components/schemas/CreatePondRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pond updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/PondResponse')
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - Not pond owner',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Pond not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
    #[OA\Delete(
        path: '/pond/delete/{id}',
        summary: 'Delete Pond',
        description: 'Delete pond permanently (only owner can delete)',
        security: [['sanctum' => []]],
        tags: ['Pond'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Pond ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pond deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Tambak berhasil dihapus!'),
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - Not pond owner',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Pond not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
