<?php

namespace App\Http\Controllers\Api\V1\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\UpdateUserPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateController extends Controller
{
    public function __invoke(UpdateUserPasswordRequest $updateUserPasswordRequest): JsonResponse
    {
        $validatedPasswordData = $updateUserPasswordRequest->validated();

        auth()->user()->update([
           'password' => Hash::make($validatedPasswordData['password']),
        ]);

        return response()->json([
            'message' => 'Your password has been updated',
        ], Response::HTTP_ACCEPTED);
    }
}
