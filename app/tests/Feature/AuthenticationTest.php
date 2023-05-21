<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @small
    */
    public function testUserCannotRegisterWithIncorrectCredentials()
    {
        $response = $this->postJson(static::API_URL_PREFIX . 'auth/register', [
            'name'                  => 'John Smith',
            'email'                 => 'john@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password_wrong',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'name'  => 'John Smith',
            'email' => 'john@example.com'
        ]);
    }

    /**
     * @depends testUserCannotRegisterWithIncorrectCredentials
     * @small
    */
    public function testUserCanRegisterWithCorrectCredentials()
    {
        $response = $this->postJson(static::API_URL_PREFIX . 'auth/register', [
            'name'                  => 'John Smith',
            'email'                 => 'john@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token'
            ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'John Smith',
            'email' => 'john@example.com'
        ]);
    }

    /**
     * @depends testUserCanRegisterWithCorrectCredentials
     * @small
    */
    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(static::API_URL_PREFIX . 'auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);
    }

    /**
     * @depends testUserCanLoginWithCorrectCredentials
     * @small
    */
    public function testUserCannotLoginWithIncorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(static::API_URL_PREFIX . 'auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }
}
