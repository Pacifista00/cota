<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'keasaman' => $this->keasaman,
            'kekeruhan' => $this->kekeruhan,
            'suhu' => $this->suhu,
            'waktu' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
