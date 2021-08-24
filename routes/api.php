<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;



Route::middleware('auth:api')->group(function () {

    Route::apiResources([
        'posts' => PostController::class,
        'users' => UserController::class,
    ]);

});
