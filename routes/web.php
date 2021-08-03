<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\AppController;

Auth::routes();

Route::get('{any}', [AppController::class, 'index'])
    ->where('any', '.*')
    ->middleware(Authenticate::class)
    ->name('home');
