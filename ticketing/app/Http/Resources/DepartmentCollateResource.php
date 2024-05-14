<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentCollateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'department'=> $this->department,
            'tickets_made_today' => $this->tickets_made_today,
            'tickets_resolved_today' => $this->tickets_resolved_today,
            'tickets_made_total' => $this->tickets_made_total,
            'tickets_resolved_total' => $this->tickets_resolved_total,
            'complexity' => $this->complexity,
            'time' => $this->time,
        ];
    }
}
