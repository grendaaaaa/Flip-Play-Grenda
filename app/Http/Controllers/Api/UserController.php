<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan semua user (bisa sertakan koleksi via ?include_collection=true)
    public function index(Request $request): JsonResponse
    {
        $query = User::withCount('media');

        // Eager load relasi koleksi jika diminta
        if ($request->boolean('include_collection')) {
            $query->with('media');
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengguna berhasil dimuat.',
            'data' => UserResource::collection($query->get()),
        ], 200);
    }

    // Tambah pengguna baru
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => "Pengguna '{$user->name}' berhasil ditambahkan ke FlipPlay.",
            ])
            ->response()
            ->setStatusCode(201);
    }

    // Tampilkan detail 1 pengguna
    public function show(int $id): JsonResponse
    {
        $user = User::withCount('media')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'Detail pengguna berhasil dimuat.',
            ])
            ->response();
    }

    // Hapus pengguna dari sistem
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Pengguna '{$userName}' berhasil dihapus dari sistem.",
        ], 200);
    }
}
