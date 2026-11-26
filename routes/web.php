<?php // Mendaftarkan route halaman web bawaan untuk mengecek server aktif di browser.

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
