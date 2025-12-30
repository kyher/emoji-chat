<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public static $wrap = null;
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
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'workspace' => $this->workspace->name,
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
