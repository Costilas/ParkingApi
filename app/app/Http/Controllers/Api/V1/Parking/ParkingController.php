<?php

namespace App\Http\Controllers\Api\V1\Parking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parking\StartParkingRequest;
use App\Http\Resources\Parking\ParkingResource;
use App\Models\Parking;
use App\Services\Price\ParkingPriceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Parking
 */
class ParkingController extends Controller
{
    /**
     * @description Start parking new parking.
     * @authenticated
    */
    public function start(StartParkingRequest $request)
    {
        $parkingData = $request->validated();

        if(Parking::active()->where('vehicle_id', $request->vehicle_id)->exists()) {

            $return = response()->json([
                    'errors' => [
                        'general' => ['Can\'t start parking twice using same vehicle. Please stop currently active parking.'],
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }else {

            $parking = Parking::create($parkingData);
            $parking->load('vehicle', 'zone');

            $return = ParkingResource::make($parking);
        }

        return $return;
    }

    /**
     * @description Stop parking.
     * @authenticated
     */
    public function stop(Parking $parking)
    {
        if($parking->end_time) {

            $returnJson = response()->json(
                ['errors' => ['general' => ['Parking already stopped!']]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        } else {
            $parking->update([
                'end_time' => now(),
                'total_price' => ParkingPriceService::calculatePrice($parking->zone_id, $parking->start_time),
            ]);

            $returnJson = ParkingResource::make($parking);
        }

        return $returnJson;
    }

    /**
     * @description Show particular parking data.
     * @authenticated
     */
    public function show(Parking $parking)
    {
        return ParkingResource::make($parking);
    }
}
