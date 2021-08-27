<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Friend;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use App\Exceptions\UserNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'friend_id' => ''
        ]);

        try {
            User::findOrFail($data['friend_id'])
                ->friends()
                ->attach(auth()->user());
        } catch (ModelNotFoundException $exception) {
            throw new UserNotFoundException();
        }

        $friend = Friend::where('user_id', auth()->user()->id)
            ->where('friend_id', $data['friend_id'])
            ->first();

        return new FriendResource($friend);
    }
}
