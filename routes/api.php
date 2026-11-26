<?php

use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for FlipPlay
|--------------------------------------------------------------------------
*/

// Standar CRUD Media Endpoints
Route::apiResource('media', MediaController::class);

// Manajemen Pengguna (Users)
Route::get('users', [App\Http\Controllers\Api\UserController::class, 'index'])->name('users.index');
Route::post('users', [App\Http\Controllers\Api\UserController::class, 'store'])->name('users.store');
Route::get('users/{id}', [App\Http\Controllers\Api\UserController::class, 'show'])->name('users.show');

// Custom Actions: User Media Collections
Route::prefix('users/{userId}/collection')->group(function () {
    // 1. Menampilkan koleksi media seorang pengguna
    Route::get('/', [CollectionController::class, 'index'])->name('users.collection.index');

    // 2. Menambahkan media ke dalam koleksi pengguna
    Route::post('/', [CollectionController::class, 'addMedia'])->name('users.collection.store');

    // 3. Menghapus media dari koleksi pengguna
    Route::delete('/{mediaId}', [CollectionController::class, 'removeMedia'])->name('users.collection.destroy');
});
