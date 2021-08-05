<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_cat_post_a_text_post()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $response = $this->post('/api/posts', [
            'data' => [
                'type' => 'posts',
                'attributes' => [
                    'body' => 'Testing Body',
                ],
            ]
        ]);

        $post = Post::first();

        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing Body', $post->body);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    'attributes' => [
                        'posted_by' => [
                            'data' => [
                                'type' => 'users',
                                'user_id' => $user->id,
                                'attributes' => [
                                    'name' => $user->name,
                                ],
                            ],
                            'links' => [
                                'self' => url('/users/'.$user->id),
                            ]
                        ],
                        'body' => 'Testing Body'
                    ],
                ],
                'links' => [
                    'self' => url('/posts/'.$post->id)
                ]
            ]);
    }
}
