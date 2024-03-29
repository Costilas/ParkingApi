<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Database\Seeders\ZoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ZoneSeeder::class);
    }

    /**
     * @small
    */
    public function testUserCanStartParking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id
        ]);
        $zone = Zone::first();

        $response = $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'zone' => [
                        'name' => $zone->name,
                        'price_per_hour' => $zone->price_per_hour,
                    ],
                    'vehicle' => [
                        'plate_number' => $vehicle->plate_number,
                    ],
                    'start_time' => now()->toDateTimeString(),
                    'end_time' => null,
                    'total_price' => 0,
                ],
            ]);

        $this->assertDatabaseCount('parkings', '1');
    }

    /**
     * @small
     */
    public function testUserCanGetOngoingParkingWithCorrectPrice()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $hoursPassed = 2;

        $this->travel($hoursPassed)->hours();

        $parking = Parking::first();
        $response = $this->actingAs($user)->getJson('/api/v1/parkings/' . $parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'zone' => [
                        'name' => $zone->name,
                        'price_per_hour' => $zone->price_per_hour,
                    ],
                    'vehicle' => [
                        'plate_number' => $vehicle->plate_number,
                    ],
                    'start_time' => $parking->start_time,
                    'end_time' => null,
                    'total_price' => $zone->price_per_hour * $hoursPassed,
                ],
            ]);
    }

    /**
     * @small
     */
    public function testUserCanStopParking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $this->travel(2)->hours();

        $parking = Parking::first();
        $response = $this->actingAs($user)->putJson('/api/v1/parkings/' . $parking->id);

        $updatedParking = Parking::find($parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'id' => $updatedParking->id,
                    'zone' => [
                        'name' => $zone->name,
                        'price_per_hour' => $zone->price_per_hour,
                    ],
                    'vehicle' => [
                        'plate_number' => $vehicle->plate_number,
                    ],
                    'start_time' => $updatedParking->start_time,
                    'end_time' => $updatedParking->end_time,
                    'total_price' => $updatedParking->total_price,
                ],
            ]);

        $this->assertDatabaseCount('parkings', '1');
    }
}
