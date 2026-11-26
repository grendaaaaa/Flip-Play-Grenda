<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserCollectionResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    // Tampilkan daftar koleksi media milik pengguna
    public function index(Request $request, int $userId): JsonResponse
    {
        $user = User::with(['media'])->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        return (new UserCollectionResource($user))
            ->additional([
                'success' => true,
                'message' => "Koleksi media untuk {$user->name} berhasil dimuat.",
            ])
            ->response();
    }

    // Custom Action: Tambah/update media ke koleksi pengguna (attach / updateExistingPivot)
    public function addMedia(AddToCollectionRequest $request, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        $mediaId = $request->input('media_id');
        $media = Media::find($mediaId);

        $alreadyInCollection = $user->media()->where('media_id', $mediaId)->exists();

        $pivotAttributes = [
            'status' => $request->input('status', 'watching'),
            'notes' => $request->input('notes'),
            'personal_rating' => $request->input('personal_rating'),
        ];

        // Jika sudah ada, perbarui datanya. Jika belum, tambahkan relasi baru
        if ($alreadyInCollection) {
            $user->media()->updateExistingPivot($mediaId, $pivotAttributes);
            $message = "'{$media->title}' sudah ada di koleksi, data berhasil diperbarui.";
            $statusCode = 200;
        } else {
            $user->media()->attach($mediaId, $pivotAttributes);
            $message = "'{$media->title}' berhasil ditambahkan ke koleksi {$user->name}.";
            $statusCode = 201;
        }

        $collectedMedia = $user->media()->where('media_id', $mediaId)->first();

        return (new MediaResource($collectedMedia))
            ->additional([
                'success' => true,
                'message' => $message,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ])
            ->response()
            ->setStatusCode($statusCode);
    }

    // Custom Action: Hapus media dari koleksi pengguna (detach)
    public function removeMedia(int $userId, int $mediaId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        $media = Media::find($mediaId);
        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media tidak ditemukan.',
            ], 404);
        }

        $exists = $user->media()->where('media_id', $mediaId)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => "Media '{$media->title}' tidak ditemukan di koleksi {$user->name}.",
            ], 404);
        }

        $user->media()->detach($mediaId);

        return response()->json([
            'success' => true,
            'message' => "Media '{$media->title}' berhasil dihapus dari koleksi {$user->name}.",
        ], 200);
    }
}
