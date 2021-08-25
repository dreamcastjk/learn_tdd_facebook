<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Collections\PostCollection;

class UserPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return JsonResource
     */
    public function index(User $user): JsonResource
    {
        return new PostCollection($user->posts);
    }
}
