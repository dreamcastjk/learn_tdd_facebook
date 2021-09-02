<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FriendRequestRequest;
use App\Models\User;
use App\Models\Friend;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use App\Exceptions\UserNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendRequestController extends Controller
{
    public function store(FriendRequestRequest $request)
    {
        try {
            User::findOrFail($request['friend_id'])
                ->friends()
                ->syncWithoutDetaching(auth()->user());
        } catch (ModelNotFoundException $exception) {
            throw new UserNotFoundException();
        }

        $friend = Friend::where('user_id', auth()->user()->id)
            ->where('friend_id', $request['friend_id'])
            ->first();

        return new FriendResource($friend);
    }
}
