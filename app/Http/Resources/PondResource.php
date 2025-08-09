<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PondResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'lokasi' => $this->lokasi,
            'token_tambak' => $this->token_tambak,
            'status_koneksi' => $this->status_koneksi,
            'status_perangkat' => $this->status_perangkat,
        ];
    }
}
