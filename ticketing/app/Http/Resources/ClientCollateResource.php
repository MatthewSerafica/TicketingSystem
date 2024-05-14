<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientCollateResource extends JsonResource
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
            'employee' => [
                'department' => $this->employee->department,
                'office' => $this->employee->office,
                'made_ticket' => $this->employee->made_ticket,
            ],
            'average_resolution_time' => $this->average_resolution_time,
            'complexity_counts' => $this->complexity_counts,
            'resolved_total' => $this->resolved_total,
            'assigned_today' => $this->assigned_today,
            'resolved_today' => $this->resolved_today,
        ];
    }
}
