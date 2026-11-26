<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    // Nama tabel ditulis eksplisit agar tidak tertukar dengan bentuk tunggal medium.
    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'creator',
        'synopsis',
        'total_units',
        'status',
        'release_year',
    ];

    protected $casts = [
        'total_units' => 'integer',
        'release_year' => 'integer',
    ];

    // Format angka menjadi Episode (Anime) atau Volume (Manga) sesuai konsep Modul 3.
    protected function unitLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf(
                '%d %s',
                $this->total_units,
                strtolower((string) $this->type) === 'manga' ? 'Volume' : 'Episode'
            ),
        );
    }

    public function scopeSearchByTitle(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->where('title', 'like', '%' . $keyword . '%');
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        $type = trim((string) $type);

        if ($type === '') {
            return $query;
        }

        return $query->whereRaw('LOWER(type) = ?', [strtolower($type)]);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withTimestamps();
    }
}
