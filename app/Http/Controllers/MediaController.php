<?php // Controller untuk operasi CRUD katalog media dan filter pencarian judul.

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // Mendukung filter judul lewat query ?title= maupun ?search=.
        $keyword = $request->query('title', $request->query('search'));
        $type = $request->query('type');

        $media = Media::query()
            ->withCount('users')
            ->searchByTitle($keyword)
            ->ofType($type)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media->loadCount('users')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Media $media): MediaResource
    {   //eager loading data pengoleksinya langsung terambil sekaligus
        $media->load('users')->loadCount('users');

        return new MediaResource($media);
    }

    public function update(StoreMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media->refresh()->load('users')->loadCount('users'));
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus dari sistem FlipPlay.',
        ], 200);
    }
}
