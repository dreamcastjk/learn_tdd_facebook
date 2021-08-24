<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\Pure;

class UserController extends Controller
{
    /**
     * @param User $user
     * @return JsonResource
     */
    public function show(User $user): JsonResource
    {
        return new UserResource($user);
    }
}
