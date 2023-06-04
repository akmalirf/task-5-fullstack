<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Faker\Factory as Faker;

class AuthenticationTest extends TestCase
{
    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testSuccessfulRegistration()
    {
        $faker = Faker::create();

        $userData = [
            "name" => $faker->name,
            "email" => $faker->unique()->safeEmail,
            "password" => $faker->password(8),
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "user" => [
                    'name',
                    'email',
                    'updated_at',
                    'created_at',
                    'id',
                ],
                "token",
                "message"
            ]);
    }
}
