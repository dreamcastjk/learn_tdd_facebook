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

    /**
     * @test
     */
    public function only_valid_friend_requests_cant_be_accepted()
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser, 'api')
            ->post(route('friend-request-response.store'), [
                'user_id' => 123,
                'status' => 1,
            ])
            ->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'status' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information',
            ],
        ]);
    }

    /**
     * @test
     */
    public function only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotherUser = User::factory()->create();
        $this->post(route('friend-request.store'), [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(User::factory()->create(), 'api')
            ->post(route('friend-request-response.store'), [
                'user_id' => $user->id,
                'status' => 1,
            ])
            ->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'status' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information',
            ],
        ]);
    }

    /**
     * @test
     */
    public function a_friend_id_is_required_for_friend_requests()
    {
        $response = $this->actingAs($user = User::factory()->create(), 'api')
            ->post(route('friend-request.store'), [
                'friend_id' => '',
            ])->assertStatus(422);

        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseArray['errors']['meta']);
    }

    /**
     * @test
     */
    public function a_user_id_and_status_is_required_for_friend_request_responses()
    {
        $response = $this->actingAs($user = User::factory()->create(), 'api')
            ->post(route('friend-request-response.store'), [
                'user_id' => '',
                'status' => ''
            ])
            ->assertStatus(422);

        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseArray['errors']['meta']);
        $this->assertArrayHasKey('status', $responseArray['errors']['meta']);
    }

    /**
     * @test
     */
    public function a_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotherUser = User::factory()->create();
        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1
        ]);

        $this->get(route('users.show', $anotherUser->id))
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ],
                    ],
                ],
            ]);
    }

    /**
     * @test
     */
    public function an_inverse_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotherUser = User::factory()->create();
        $friendRequest = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1
        ]);

        $this->get(route('users.show', $anotherUser->id))
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ],
                    ],
                ],
            ]);
    }

    /**
     * @test
     */
    public function friend_requests_can_be_ignored()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $userThatSendFriendRequest = User::factory()->create();
        $this->post(route('friend-request.store'), [
            'friend_id' => $userThatSendFriendRequest->id,
        ])->assertStatus(200);

        $response = $this->actingAs($userThatSendFriendRequest, 'api')
            ->delete(route('friend-request-response.destroy', ['delete']), [
                'user_id' => $user->id,
            ])
            ->assertStatus(204);

        $friendRequest = Friend::first();

        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }

    /**
     * @test
     */
    public function only_the_recipient_can_ignore_a_friend_request()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $anotherUser = User::factory()->create();
        $this->post(route('friend-request.store'), [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(User::factory()->create(), 'api')
            ->delete(route('friend-request-response.destroy', ['delete']), [
                'user_id' => $user->id,
            ])
            ->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'status' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information',
            ],
        ]);
    }

    /**
     * @test
     */
    public function a_user_id_is_required_for_ignoring_a_friend_request_responses()
    {
        $response = $this->actingAs(User::factory()->create(), 'api')
            ->delete(route('friend-request-response.destroy', ['delete']), [
                'user_id' => '',
            ])
            ->assertStatus(422);

        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseArray['errors']['meta']);
    }
}
