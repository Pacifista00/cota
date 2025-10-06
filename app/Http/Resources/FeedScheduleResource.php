<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'waktu_pakan' => $this->waktu_pakan,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'frequency_type' => $this->frequency_type?->value,
            'frequency_type_label' => $this->frequency_type?->label(),
            'frequency_data' => $this->frequency_data,
            'last_executed_at' => $this->last_executed_at?->format('Y-m-d'),
            'next_execution' => $this->next_execution?->format('Y-m-d H:i:s'),
            'remaining_days' => $this->remaining_days,
            'is_valid' => $this->isValid(),
            'was_executed_today' => $this->wasExecutedToday(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'executions' => FeedExecutionResource::collection($this->whenLoaded('executions')),
        ];
    }
}
