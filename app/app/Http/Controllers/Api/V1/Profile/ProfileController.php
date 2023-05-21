<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateUserProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Profile
 */
class ProfileController extends Controller
{
    /**
     * @description Show profile data of authenticated user.
     * @authenticated
    */
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user()->only('name', 'email'));
    }

    /**
     * @description Update profile data of authenticated user.
     * @authenticated
     */
    public function update(UpdateUserProfileRequest $updateUserProfileRequest): JsonResponse
    {
        $validatedData = $updateUserProfileRequest->validated();

        auth()->user()->update($validatedData);

        return response()->json($validatedData, Response::HTTP_ACCEPTED);
    }
}
