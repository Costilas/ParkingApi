<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @small
     */
    public function testUserCanGetTheirOwnVehicles()
    {
        $userOne = User::factory()->create();
        $newVehicleForUserOne = Vehicle::factory([
            'user_id' => $userOne->id,
        ])->create();

        $userTwo = User::factory()->create();
        $newVehicleForUserTwo = Vehicle::factory([
            'user_id' => $userTwo->id,
        ])->create();

        $response = $this->actingAs($userOne)->getJson(self::API_URL_PREFIX . 'vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.plate_number', $newVehicleForUserOne->plate_number)
            ->assertJsonMissing($newVehicleForUserTwo->toArray());

    }

    /**
     * @small
     */
    public function testUserCanCreateVehicle()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => 'AAA111',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA111');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA111',
        ]);
    }

    /**
     * @small
     */
    public function testUserCanUpdateTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/' . $vehicle->id, [
            'plate_number' => 'AAA123',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['plate_number'])
            ->assertJsonPath('plate_number', 'AAA123');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA123',
        ]);
    }

    /**
     * @small
     */
    public function testUserCanDeleteTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/v1/vehicles/' . $vehicle->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
            'deleted_at' => NULL
        ])->assertDatabaseCount('vehicles', 1); // we have SoftDeletes, remember?
    }
}
