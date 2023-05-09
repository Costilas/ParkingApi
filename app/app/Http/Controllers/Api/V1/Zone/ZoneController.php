<?php

namespace App\Http\Controllers\Api\V1\Zone;

use App\Http\Controllers\Controller;
use App\Http\Resources\Zone\ZoneResource;
use App\Models\Zone;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ZoneController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ZoneResource::collection(Zone::all());
    }
}
