<?php

namespace Tests\Feature;

use Database\Seeders\ZoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(ZoneSeeder::class);
    }

    public function testGuestHasAccessToAllParkingZones()
    {
        $response = $this->get(static::API_URL_PREFIX . 'zones');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    [ '*' => 'id', 'name', 'price_per_hour']
                ]
            ])
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.0.name', 'Green Zone')
            ->assertJsonPath('data.0.price_per_hour', '100');
    }
}
