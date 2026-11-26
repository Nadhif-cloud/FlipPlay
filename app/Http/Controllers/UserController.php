<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()
            ->withCount('media')
            ->orderBy('id')
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return (new UserResource($user->loadCount('media')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        $user->load('media')->loadCount('media');

        return new UserResource($user);
    }

    public function update(StoreUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user->refresh()->load('media')->loadCount('media'));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus dari sistem FlipPlay.',
        ], 200);
    }

    public function collection(User $user): AnonymousResourceCollection
    {
        $collection = $user->media()
            ->orderByPivot('created_at', 'desc')
            ->get();

        return MediaResource::collection($collection)->additional([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
            ],
        ]);
    }

    public function addToCollection(AddToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        // Lock the user row inside a transaction to prevent concurrent duplicate collection inserts.
        return DB::transaction(function () use ($user, $mediaId): JsonResponse {
            $lockedUser = User::where('id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedUser->media()->where('media_id', $mediaId)->exists()) {
                return response()->json([
                    'message' => 'Media sudah ada di koleksi pengguna ini.',
                ], 409);
            }

            $lockedUser->media()->attach($mediaId);

            return response()->json([
                'message' => 'Media berhasil ditambahkan ke koleksi pengguna.',
                'data' => new UserResource($lockedUser->load('media')->loadCount('media')),
            ], 200);
        });
    }

    public function removeFromCollection(User $user, Media $media): JsonResponse
    {
        return DB::transaction(function () use ($user, $media): JsonResponse {
            $lockedUser = User::where('id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedUser->media()->where('media_id', $media->id)->exists()) {
                return response()->json([
                    'message' => 'Media tidak ditemukan di dalam koleksi pengguna ini.',
                ], 404);
            }

            $lockedUser->media()->detach($media->id);

            return response()->json([
                'message' => 'Media berhasil dihapus dari koleksi pengguna.',
                'data' => new UserResource($lockedUser->load('media')->loadCount('media')),
            ], 200);
        });
    }
}
