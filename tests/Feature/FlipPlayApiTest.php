<?php // Pengujian otomatis seluruh skenario berhasil dan gagal pada endpoint API FlipPlay.

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_media_and_reject_empty_or_duplicate_title(): void
    {
        $payload = [
            'title' => 'Blue Period',
            'type' => 'Manga',
            'genre' => 'Drama, Slice of Life',
            'creator' => 'Tsubasa Yamaguchi',
            'synopsis' => 'Siswa SMA menemukan hasrat melukis dan berjuang masuk universitas seni Tokyo.',
            'total_units' => 15,
            'status' => 'Ongoing',
            'release_year' => 2017,
        ];

        $this->postJson('/api/media', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Blue Period')
            ->assertJsonPath('data.format_detail', '15 Volume');

        $this->postJson('/api/media', ['title' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'type', 'genre', 'creator', 'total_units', 'status']);

        $this->postJson('/api/media', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_list_filter_show_update_and_delete_media(): void
    {
        $user = User::create([
            'name' => 'Nadif',
            'username' => 'nadif',
            'email' => 'nadif@flipplay.test',
        ]);

        $frieren = Media::create([
            'title' => "Frieren: Beyond Journey's End",
            'type' => 'Anime',
            'genre' => 'Adventure, Fantasy',
            'creator' => 'Kanehito Yamada',
            'synopsis' => 'Perjalanan penyihir elf.',
            'total_units' => 28,
            'status' => 'Completed',
            'release_year' => 2023,
        ]);

        Media::create([
            'title' => 'Witch Hat Atelier',
            'type' => 'Manga',
            'genre' => 'Fantasy',
            'creator' => 'Kamome Shirahama',
            'synopsis' => 'Rahasia lingkaran sihir.',
            'total_units' => 13,
            'status' => 'Ongoing',
            'release_year' => 2016,
        ]);

        $user->media()->attach($frieren->id);

        $this->getJson('/api/media')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/media?title=Frieren')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', "Frieren: Beyond Journey's End");

        $this->getJson("/api/media/{$frieren->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.collectors_count', 1)
            ->assertJsonPath('data.collected_by.0.username', 'nadif');

        $this->getJson('/api/media/9999')
            ->assertStatus(404);

        $this->putJson("/api/media/{$frieren->id}", [
            'title' => "Frieren: Beyond Journey's End",
            'type' => 'Anime',
            'genre' => 'Adventure, Fantasy, Drama',
            'creator' => 'Kanehito Yamada',
            'synopsis' => 'Perjalanan penyihir elf setelah era pahlawan usai.',
            'total_units' => 29,
            'status' => 'Completed',
            'release_year' => 2023,
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.total_units', 29);

        $this->patchJson("/api/media/{$frieren->id}", ['total_units' => 30])
            ->assertStatus(200)
            ->assertJsonPath('data.total_units', 30)
            ->assertJsonPath('data.format_detail', '30 Episode');

        $this->deleteJson("/api/media/{$frieren->id}")
            ->assertStatus(200)
            ->assertJsonPath('message', 'Media berhasil dihapus dari sistem FlipPlay.');

        $this->assertDatabaseMissing('media', ['id' => $frieren->id]);
        $this->assertDatabaseMissing('media_user', ['media_id' => $frieren->id]);
    }

    public function test_collection_custom_actions_handle_happy_and_failure_paths(): void
    {
        $user = User::create([
            'name' => 'Nadif',
            'username' => 'nadif',
            'email' => 'nadif@flipplay.test',
        ]);

        $media = Media::create([
            'title' => 'SPY x FAMILY',
            'type' => 'Anime',
            'genre' => 'Action, Comedy',
            'creator' => 'Tatsuya Endo',
            'synopsis' => 'Keluarga samaran mata-mata.',
            'total_units' => 37,
            'status' => 'Ongoing',
            'release_year' => 2022,
        ]);

        $this->postJson("/api/users/{$user->id}/collection", ['media_id' => $media->id])
            ->assertStatus(200)
            ->assertJsonPath('data.collection_count', 1);

        $this->postJson("/api/users/{$user->id}/collection", ['media_id' => $media->id])
            ->assertStatus(409)
            ->assertJsonPath('message', 'Media sudah ada di koleksi pengguna ini.');

        $this->postJson("/api/users/{$user->id}/collection", ['media_id' => 9999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);

        $this->getJson("/api/users/{$user->id}/collection")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('user.username', 'nadif');

        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.collection_count', 0);

        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(404);
    }
}
