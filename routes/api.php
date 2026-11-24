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

// Custom Actions: User Media Collections
Route::prefix('users/{userId}/collection')->group(function () {
    // 1. Menampilkan koleksi media seorang pengguna
    Route::get('/', [CollectionController::class, 'index'])->name('users.collection.index');

    // 2. Menambahkan media ke dalam koleksi pengguna
    Route::post('/', [CollectionController::class, 'addMedia'])->name('users.collection.store');

    // 3. Menghapus media dari koleksi pengguna
    Route::delete('/{mediaId}', [CollectionController::class, 'removeMedia'])->name('users.collection.destroy');
});
