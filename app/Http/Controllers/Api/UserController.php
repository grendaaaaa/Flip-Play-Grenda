<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user (bisa dengan query ?include_collection=true).
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = User::withCount('media');

        // Jika client meminta menyertakan daftar koleksinya
        if ($request->boolean('include_collection')) {
            $query->with('media');
        }

        $users = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengguna berhasil dimuat.',
            'data' => UserResource::collection($users),
        ], 200);
    }

    /**
     * Menambahkan user baru ke FlipPlay.
     */
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

    /**
     * Menampilkan detail 1 user.
     */
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

    /**
     * Menghapus user dari FlipPlay (beserta koleksinya otomatis cascade).
     */
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
