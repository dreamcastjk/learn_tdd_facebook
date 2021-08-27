<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthUserController;
use App\Http\Controllers\Api\UserPostController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\FriendRequestResponseController;


Route::middleware('auth:api')->group(function () {

    Route::get('auth-user', AuthUserController::class)->name('auth.user');

    Route::apiResources([
        'posts' => PostController::class,
        'users' => UserController::class,
        'friend-request' => FriendRequestController::class,
        'friend-request-response' => FriendRequestResponseController::class,
    ]);

    Route::apiResource('users.posts', UserPostController::class, ['name' => 'users.posts']);
});
