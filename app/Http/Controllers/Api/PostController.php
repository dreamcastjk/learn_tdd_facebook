<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\PostCollection;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class PostController extends Controller
{
    /**
     * @return PostResource
     */
    public function store(PostStoreRequest $request): JsonResource
    {
        $post = $request->user()->posts()->create($request['data']['attributes']);

        return new PostResource($post);
    }

    public function index(): JsonResource
    {
        return new PostCollection(Post::paginate());
    }
}
