<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    // Ambil daftar media (dukung pencarian judul, filter tipe/genre, & urutan)
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Media::query()->withCount('users');

        // Filter judul
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where('title', 'like', "%{$searchTerm}%");
        }

        // Filter tipe media
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        // Filter genre
        if ($request->filled('genre')) {
            $query->where('genre', 'like', "%{$request->query('genre')}%");
        }

        // Urutkan data
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('order', 'desc');
        if (in_array($sortBy, ['title', 'rating', 'release_year', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        return MediaResource::collection($query->paginate($request->query('per_page', 10)));
    }

    // Tambah media baru
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional([
                'success' => true,
                'message' => 'Media berhasil ditambahkan ke katalog FlipPlay.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    // Tampilkan detail 1 media beserta pengguna yang mengoleksinya
    public function show(int $id): JsonResponse
    {
        $media = Media::with(['users'])->withCount('users')->find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media tidak ditemukan.',
            ], 404);
        }

        return (new MediaResource($media))
            ->additional([
                'success' => true,
                'message' => 'Detail media berhasil dimuat.',
            ])
            ->response();
    }

    // Update data media
    public function update(UpdateMediaRequest $request, int $id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media tidak ditemukan.',
            ], 404);
        }

        $media->update($request->validated());

        return (new MediaResource($media))
            ->additional([
                'success' => true,
                'message' => 'Data media berhasil diperbarui.',
            ])
            ->response();
    }

    // Hapus media dari sistem
    public function destroy(int $id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media tidak ditemukan.',
            ], 404);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => "Media '{$media->title}' berhasil dihapus dari sistem.",
        ], 200);
    }
}
