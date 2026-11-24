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
    /**
     * Menampilkan daftar seluruh media dalam koleksi pengguna tertentu.
     */
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

    /**
     * Custom Action: Menambahkan media ke dalam koleksi pribadi pengguna.
     */
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

        // Periksa apakah sudah ada di koleksi
        $alreadyInCollection = $user->media()->where('media_id', $mediaId)->exists();

        $pivotAttributes = [
            'status' => $request->input('status', 'watching'),
            'notes' => $request->input('notes'),
            'personal_rating' => $request->input('personal_rating'),
        ];

        if ($alreadyInCollection) {
            // Update status & notes yang ada
            $user->media()->updateExistingPivot($mediaId, $pivotAttributes);
            $message = "'{$media->title}' sudah ada di koleksi, data status/catatan berhasil diperbarui.";
            $statusCode = 200;
        } else {
            // Pasang media ke user collection
            $user->media()->attach($mediaId, $pivotAttributes);
            $message = "'{$media->title}' berhasil ditambahkan ke koleksi {$user->name}.";
            $statusCode = 201;
        }

        // Ambil data media terbaru beserta pivot datanya
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

    /**
     * Custom Action: Menghapus media dari koleksi pengguna.
     */
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
