<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zonesData = [
            ['name' => 'Green Zone', 'price_per_hour' => 100],
            ['name' => 'Yellow Zone', 'price_per_hour' => 200],
            ['name' => 'Red Zone', 'price_per_hour' => 300],
        ];

        foreach ($zonesData as $zone) {
            Zone::create($zone);
        }
    }
}
