<?php

namespace App\Services\Price;

use App\Models\Zone;
use Carbon\Carbon;

class ParkingPriceService
{
    public static function calculatePrice(int $zone_id, string $startTime, string $endTime = null): int
    {
        $start = new Carbon($startTime);
        $end= $endTime
            ? new Carbon($endTime)
            : now();

        $totalTimeByHours = $end->diffInMinutes($start);
        $pricePerHour = Zone::find($zone_id)?->price_per_hour / 60;

        return ceil($totalTimeByHours * $pricePerHour);
    }
}
