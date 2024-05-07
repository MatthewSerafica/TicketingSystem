<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollateResource extends JsonResource
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
            'name' => $this->name,
            'avatar' => $this->avatar,
            'technician' => [
                'assigned' => $this->technician->tickets_assigned,
                'resolved' => $this->technician->tickets_resolved,
            ],
            'yearly_data' => $this->yearly_data,
            'average_resolution_time' => $this->average_resolution_time,
            'complexity_counts' => $this->complexity_counts,
            'resolved_today' => $this->resolved_today,
            'assigned_today' => $this->assigned_today,
        ];
    }
}
