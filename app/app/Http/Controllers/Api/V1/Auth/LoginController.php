<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
*/
class LoginController extends Controller
{
    /**
     * @description Log in user endpoint.
     * @throws ValidationException
     */
    public function __invoke(LoginUserRequest $request): JsonResponse
    {
       $validatedData = $request->validated();

       $user = User::where('email', $validatedData['email'])->first();

       if(
           ! $user
           || ! Hash::check($validatedData['password'], $user->password)
       ) {
           throw ValidationException::withMessages([
               'email' => ['The provided credentials are incorrect.'],
           ]);
       }

       $device = substr($request->userAgent() ?? '', 0, 255);
       $expriesAt = $request->remember
           ? null
           : now()->addMinutes(config('session.lifetime'));

       return response()->json([
           'access_token' => $user->createToken($device, expiresAt: $expriesAt)->plainTextToken,
       ], Response::HTTP_CREATED);
    }
}
