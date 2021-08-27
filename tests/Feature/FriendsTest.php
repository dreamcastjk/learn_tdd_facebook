<?php

namespace Tests\Feature;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $response = $this->post('/api/friend-request', [
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

        $response = $this->post('/api/friend-request', [
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
}
