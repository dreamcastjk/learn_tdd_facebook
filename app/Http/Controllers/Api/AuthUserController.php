<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function __invoke()
    {

        return new UserResource(auth()->user());
    }
}
