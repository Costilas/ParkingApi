<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Response;

/**
 * @group Vehicles
 */
class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @authenticated
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return VehicleResource::collection(Vehicle::all());
    }

    /**
     * Store a newly created resource in storage.
     * @authenticated
     * @param  StoreVehicleRequest $request
     * @return VehicleResource
     */
    public function store(StoreVehicleRequest $request)
    {
        $newUserVehicle = Vehicle::create($request->validated());

        return VehicleResource::make($newUserVehicle);
    }

    /**
     * Display the specified resource.
     * @authenticated
     * @param  \App\Models\Vehicle  $vehicle
     * @return VehicleResource
     */
    public function show(Vehicle $vehicle)
    {
        return VehicleResource::make($vehicle);
    }

    /**
     * Update the specified resource in storage.
     * @authenticated
     * @param  StoreVehicleRequest $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());

        return response()->json(VehicleResource::make($vehicle), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     * @authenticated
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->noContent();
    }
}
