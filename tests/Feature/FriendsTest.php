<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_send_a_friend_request()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $userThatSendFriendRequest = User::factory()->create();

        $response = $this->post(route('friend-request.store'), [
            'friend_id' => $userThatSendFriendRequest->id,
        ])->assertStatus(200);

        $friendRequest = Friend::first();

        $this->assertNotNull($friendRequest);
        $this->assertEquals($userThatSendFriendRequest->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ],
            ],
            'links' => [
                'self' => url('/users/'.$userThatSendFriendRequest->id),
            ]
        ]);
    }

    /**
     * @test
     */
    public function only_valid_users_can_be_friend_requested()
    {
        $this->actingAs($user = User::factory()->create(), 'api');

        $response = $this->post(route('friend-request.store'), [
            'friend_id' => 123,
        ])->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'status' => 404,
                'title' => 'User Not Found',
                'detail' => 'Unable to locate the user with the given information',
            ],
        ]);
    }

    /**
     * @test
     */
    public function friend_request_can_be_accepted()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $userThatSendFriendRequest = User::factory()->create();
        $this->post(route('friend-request.store'), [
            'friend_id' => $userThatSendFriendRequest->id,
        ])->assertStatus(200);

        $response = $this->actingAs($userThatSendFriendRequest, 'api')
            ->post(route('friend-request-response.store'), [
                'user_id' => $user->id,
                'status' => 1,
            ])
            ->assertStatus(200);

        $friendRequest = Friend::first();

        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                ],
            ],
            'links' => [
                'self' => url('/users/'.$userThatSendFriendRequest->id),
            ]
        ]);
    }
}
