<?php // Model Eloquent untuk tabel users beserta relasi koleksi media milik pengguna.

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'email',
    ];

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')
            ->withTimestamps();
    }
}
