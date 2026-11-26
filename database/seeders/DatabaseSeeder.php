<?php // Mengisi data awal pengguna, media, dan koleksi ke database saat perintah seed dijalankan.

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $nadif = User::create([
            'name' => 'Nadif',
            'username' => 'nadif',
            'email' => 'nadif@flipplay.test',
        ]);

        $alya = User::create([
            'name' => 'Alya Rahma',
            'username' => 'alyarahma',
            'email' => 'alya@flipplay.test',
        ]);

        $rizky = User::create([
            'name' => 'Rizky Pratama',
            'username' => 'rizkypratama',
            'email' => 'rizky@flipplay.test',
        ]);

        $frieren = Media::create([
            'title' => "Frieren: Beyond Journey's End",
            'type' => 'Anime',
            'genre' => 'Adventure, Fantasy, Drama',
            'creator' => 'Kanehito Yamada',
            'synopsis' => 'Penyihir elf menelusuri kembali arti kebersamaan setelah petualangan kelompok pahlawannya berakhir.',
            'total_units' => 28,
            'status' => 'Completed',
            'release_year' => 2023,
        ]);

        $spyFamily = Media::create([
            'title' => 'SPY x FAMILY',
            'type' => 'Anime',
            'genre' => 'Action, Comedy',
            'creator' => 'Tatsuya Endo',
            'synopsis' => 'Seorang mata-mata membentuk keluarga samaran untuk menjalankan misi perdamaian rahasia.',
            'total_units' => 37,
            'status' => 'Ongoing',
            'release_year' => 2022,
        ]);

        $witchHat = Media::create([
            'title' => 'Witch Hat Atelier',
            'type' => 'Manga',
            'genre' => 'Fantasy, Adventure',
            'creator' => 'Kamome Shirahama',
            'synopsis' => 'Coco mempelajari rahasia menggambar lingkaran sihir sebagai murid di bengkel penyihir Qifrey.',
            'total_units' => 13,
            'status' => 'Ongoing',
            'release_year' => 2016,
        ]);

        $dungeonMeshi = Media::create([
            'title' => 'Delicious in Dungeon',
            'type' => 'Manga',
            'genre' => 'Adventure, Comedy, Fantasy',
            'creator' => 'Ryoko Kui',
            'synopsis' => 'Kelompok petualang bertahan hidup di dalam labirin dengan memasak monster yang mereka temui.',
            'total_units' => 14,
            'status' => 'Completed',
            'release_year' => 2014,
        ]);

        $nadif->media()->attach([$frieren->id, $witchHat->id]);
        $alya->media()->attach([$frieren->id, $spyFamily->id]);
        $rizky->media()->attach([$dungeonMeshi->id]);
    }
}
