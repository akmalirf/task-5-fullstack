<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Faker\Factory as Faker;

class ApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use DatabaseTransactions;

    public function testListAllWithPaginate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $posts = Post::factory()->count(4)->create(['user_id' => $user->id]);

        $this->json('GET', '/api/v1/posts', [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken])
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'image',
                            'user_id',
                            'category_id',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'current_page' => 1,
                    'data' => $posts->toArray(),
                    'per_page' => 5,
                    'prev_page_url' => null,
                    'to' => 4,
                    'total' => 4,
                ]
            ]);
    }

    public function testShowDetail()
    {
        $post = Post::factory()->create();
        $user = User::where('id', $post->user_id)->first();
        Passport::actingAs($user);
        $this->json('GET', '/api/v1/posts/' . $post->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $post->toArray()
            ]);
    }

    public function testCreatePost()
    {
        $faker = Faker::create();
        $category = Category::factory()->create();
        $user = User::where('id', $category->user_id)->first();
        Passport::actingAs($user);


        $postData = [
            "title" => $faker->sentence,
            "content" => $faker->paragraph,
            "image" => 'image/image.jpg',
            "category_id" => $category->id,
            "user_id" => $user->id,
        ];

        $this->json('POST', 'api/v1/posts', $postData, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'title',
                    'content',
                    'image',
                    'category_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $postData['title'],
                    'content' => $postData['content'],
                    'image' => $postData['image'],
                    'category_id' => $postData['category_id'],
                ],
            ]);
    }

    public function testUpdatePost()
    {
        $faker = Faker::create();
        $post = Post::factory()->create();
        $updatedData = [
            'title' => $faker->sentence,
        ];

        $user = User::where('id', $post->user_id)->first();
        Passport::actingAs($user);

        $this->json('PUT', '/api/v1/posts/' . $post->id, $updatedData, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken])
            ->assertStatus(200)
            ->assertJson([
                'data' => ['title' => $updatedData['title']]
            ]);

        $post->refresh();

        $this->assertEquals($updatedData['title'], $post->title);
    }

    public function testDeletePost()
    {
        $post = Post::factory()->create();
        $user = User::where('id', $post->user_id)->first();
        Passport::actingAs($user);

        $this->json('DELETE', '/api/v1/posts/' . $post->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken])
            ->assertStatus(200);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
