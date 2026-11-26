<?php

use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute API FlipPlay
|--------------------------------------------------------------------------
*/

// CRUD Master Media
Route::apiResource('media', MediaController::class);

// Manajemen Pengguna
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::post('users', [UserController::class, 'store'])->name('users.store');
Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Custom Actions: Relasi Koleksi Media Pengguna
Route::prefix('users/{userId}/collection')->group(function () {
    Route::get('/', [CollectionController::class, 'index'])->name('users.collection.index');          // Tampilkan koleksi pengguna
    Route::post('/', [CollectionController::class, 'addMedia'])->name('users.collection.store');       // Tambah media ke koleksi
    Route::delete('/{mediaId}', [CollectionController::class, 'removeMedia'])->name('users.collection.destroy'); // Hapus media dari koleksi
});
