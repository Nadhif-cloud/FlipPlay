<?php // Mengatur format output JSON data media dan daftar pengoleksinya agar konsisten.

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'creator' => $this->creator,
            'synopsis' => $this->synopsis,
            'total_units' => $this->total_units,
            'format_detail' => $this->unit_label,
            'status' => $this->status,
            'release_year' => $this->release_year,
            'collectors_count' => $this->whenCounted('users'),
            // Daftar pengoleksi hanya muncul jika relasi users sudah dimuat lewat load() atau with().
            'collected_by' => UserResource::collection($this->whenLoaded('users')),
            'added_to_collection_at' => $this->whenPivotLoaded(
                'media_user',
                fn () => $this->pivot->created_at?->toISOString()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
