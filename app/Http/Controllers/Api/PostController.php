<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\PostResource;
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
}
