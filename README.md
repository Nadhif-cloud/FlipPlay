# FlipPlay REST API (Demo Modul 4 Pemrograman Web)

FlipPlay REST API adalah pengembangan lanjutan dari simulasi OOP berbasis terminal pada Modul 3 menjadi layanan backend REST API menggunakan Laravel, Eloquent ORM, dan database SQLite. Sistem ini mengelola katalog media (`Anime` dan `Manga`) serta koleksi pribadi milik setiap pengguna (`User`) melalui relasi Many-to-Many (`belongsToMany`).

## Struktur Entitas & Database (SQLite)

File database SQLite terletak di `database/database.sqlite` dan dapat dibuka langsung menggunakan **DB Browser for SQLite**.

1. **`users`**: Menyimpan data pengguna FlipPlay (`id`, `name`, `username`, `email`, `timestamps`).
2. **`media`**: Menyimpan katalog media (`id`, `title` unik, `type`, `genre`, `creator`, `synopsis`, `total_units`, `status`, `release_year`, `timestamps`).
3. **`media_user`**: Tabel pivot relasi Many-to-Many antara `media` dan `users` (`id`, `media_id` foreign key cascade delete, `user_id` foreign key cascade delete, `timestamps`, serta constraint unik `['media_id', 'user_id']`).

## Cara Menjalankan Project

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Server berjalan di `http://127.0.0.1:8000`.

Untuk memverifikasi daftar route dan menjalankan pengujian otomatis:

```bash
php artisan route:list --path=api
php artisan test
```

## Daftar Endpoint API

### 1. Entitas Media (CRUD Standar + Query Filter)
| HTTP Method | Endpoint | Controller Method | Deskripsi | Status Sukses |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/media` | `MediaController@index` | Daftar seluruh media (mendukung filter `?title=...` dan `?type=Anime\|Manga`) | `200 OK` |
| `POST` | `/api/media` | `MediaController@store` | Menambahkan media baru ke katalog | `201 Created` |
| `GET` | `/api/media/{media}` | `MediaController@show` | Detail satu media beserta daftar pengguna pengoleksi (`whenLoaded`) | `200 OK` |
| `PATCH` | `/api/media/{media}` | `MediaController@update` | Memperbarui sebagian atau seluruh data media | `200 OK` |
| `DELETE` | `/api/media/{media}` | `MediaController@destroy` | Menghapus media dari katalog | `200 OK` |

### 2. Entitas Pengguna & Custom Action Koleksi
| HTTP Method | Endpoint | Controller Method | Deskripsi | Status Sukses |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/users` | `UserController@index` | Daftar seluruh pengguna beserta jumlah koleksi | `200 OK` |
| `POST` | `/api/users` | `UserController@store` | Menambahkan pengguna baru | `201 Created` |
| `GET` | `/api/users/{user}` | `UserController@show` | Detail pengguna beserta daftar koleksi media (`whenLoaded`) | `200 OK` |
| `PATCH` | `/api/users/{user}` | `UserController@update` | Memperbarui data pengguna | `200 OK` |
| `DELETE` | `/api/users/{user}` | `UserController@destroy` | Menghapus pengguna | `200 OK` |
| `GET` | `/api/users/{user}/collection` | `UserController@collection` | Menampilkan koleksi media pribadi milik seorang pengguna | `200 OK` |
| `POST` | `/api/users/{user}/collection` | `UserController@addToCollection` | Custom action (`attach`): menambahkan media ke koleksi pengguna | `200 OK` |
| `DELETE` | `/api/users/{user}/collection/{media}` | `UserController@removeFromCollection` | Custom action (`detach`): menghapus media dari koleksi pengguna | `200 OK` |

## Pengujian via Postman

Impor file koleksi `postman/FlipPlay_Modul4.postman_collection.json` ke aplikasi Postman. Koleksi tersebut sudah dibagi menjadi 3 bagian siap uji:
1. **Media CRUD & Filter (Happy Path)**: tambah media (`201`), daftar media (`200`), cari judul `?title=Frieren` (`200`), detail media + pengoleksi (`200`), update `PATCH` (`200`), dan hapus `DELETE` (`200`).
2. **Pengguna & Koleksi Media (Happy Path)**: tambah media ke koleksi (`attach`, `200`), lihat koleksi pengguna (`200`), dan hapus media dari koleksi (`detach`, `200`).
3. **Skenario Gagal (Failure Path)**: judul media kosong (`422`), judul media duplikat (`422`), jenis media tidak valid (`422`), ID media tidak ditemukan (`404`), menambahkan media yang sama dua kali ke koleksi pengguna yang sama (`409 Conflict`), serta menghapus media yang tidak ada di koleksi pengguna (`404`).
