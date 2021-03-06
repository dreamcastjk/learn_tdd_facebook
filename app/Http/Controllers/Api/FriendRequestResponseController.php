<?php

namespace App\Http\Controllers\Api;

use App\Models\Friend;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\IgnoreFriendRequestRequest;
use App\Exceptions\FriendRequestNotFoundException;
use App\Http\Requests\FriendRequestResponseRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendRequestResponseController extends Controller
{
    /**
     * @return JsonResource
     *
     * @throws FriendRequestNotFoundException
     */
    public function store(FriendRequestResponseRequest $request): JsonResource
    {
        try {
            $friendRequest = Friend::where('user_id', $request['user_id'])
                ->where('friend_id', auth()->user()->id)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new FriendRequestNotFoundException();
        }

        $friendRequest->update(array_merge($request->validated(), ['confirmed_at' => now()]));

        return new FriendResource($friendRequest);
    }

    public function destroy(IgnoreFriendRequestRequest $request)
    {
        try {
            Friend::where('user_id', $request['user_id'])
                ->where('friend_id', auth()->user()->id)
                ->firstOrFail()
                ->delete();
        } catch (ModelNotFoundException $exception) {
            throw new FriendRequestNotFoundException();
        }

        return response()->json([], 204);
    }
}
