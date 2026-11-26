<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollectionResource extends JsonResource
{
    // Format JSON koleksi pengguna
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
            ],
            'collection_count' => $this->media->count(),
            'collection' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
