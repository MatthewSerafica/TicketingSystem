<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            'title' => $this->title,
            'content' => $this->content,
            'tagged_user' => $this->tagged_user,
            'created_at' => $this->created_at,
            'comment_count' => $this->comment_count,
            'time_since_posted' => $this->time_since_posted,
        ];
    }
}
