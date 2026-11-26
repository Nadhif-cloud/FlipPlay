<?php // Mengatur format output JSON data pengguna dan daftar koleksi medianya agar konsisten.

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'collection_count' => $this->whenCounted('media'),
            'collection' => MediaResource::collection($this->whenLoaded('media')),
            'collected_at' => $this->whenPivotLoaded(
                'media_user',
                fn () => $this->pivot->created_at?->toISOString()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
