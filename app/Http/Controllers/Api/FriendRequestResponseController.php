<?php

namespace App\Http\Controllers\Api;

use App\Models\Friend;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Exceptions\FriendRequestNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendRequestResponseController extends Controller
{
    /**
     * @return JsonResource
     *
     * @throws FriendRequestNotFoundException
     */
    public function store(): JsonResource
    {
        $data = request()->validate([
            'user_id' => '',
            'status' => '',
        ]);

        try {
            $friendRequest = Friend::where('user_id', $data['user_id'])
                ->where('friend_id', auth()->user()->id)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new FriendRequestNotFoundException();
        }

        $friendRequest->update(array_merge($data, ['confirmed_at' => now()]));

        return new FriendResource($friendRequest);
    }
}
