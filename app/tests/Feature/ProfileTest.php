<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @small
     * @return void
     */
    public function testUserCanGetHisProfile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(static::API_URL_PREFIX . 'profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => $user->name]);
    }

    /**
     * @small
     * @return void
     */
    public function testUserCanUpdateNameAndEmail()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(static::API_URL_PREFIX . 'profile', [
            'name'  => 'John Updated',
            'email' => 'john_updated@example.com',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'John Updated', 'email' => 'john_updated@example.com']);

        $this->assertDatabaseHas('users', [
            'name' => 'John Updated',
            'email' => 'john_updated@example.com',
        ]);
    }

    /**
     * @small
     * @return void
    */
    public function testUserCanChangePassword()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(static::API_URL_PREFIX . 'password', [
            'current_password' => 'password',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(202);
    }

    /**
     * @small
     * @return void
     */
    public function testUserCannotChangePasswordWithWrongPasswordLength()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(static::API_URL_PREFIX . 'password', [
            'current_password' => 'password',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors',])
            ->assertJsonFragment([
                'message' => 'The password must be at least 8 characters.',
                'errors' => [
                    'password' => [
                        'The password must be at least 8 characters.'
                    ]
                ]
            ])->assertJsonCount(2);
    }

    /**
     * @small
     * @return void
     */
    public function testUserCannotChangePasswordWithInvalidNewPasswordConfirmation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(static::API_URL_PREFIX . 'password', [
            'current_password' => 'password',
            'password' => '12345678',
            'password_confirmation' => '12345677',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors',])
            ->assertJsonFragment([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => [
                        'The password confirmation does not match.'
                    ]
                ]
            ])->assertJsonCount(2);
    }

    /**
     * @small
     */
    public function testGuestCannotAccessProfile()
    {
        $response = $this->getJson(static::API_URL_PREFIX . 'profile');

        $response->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }
}
