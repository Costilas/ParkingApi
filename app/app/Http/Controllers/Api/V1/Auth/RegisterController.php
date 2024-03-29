<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth
 */
class RegisterController extends Controller
{
    /**
     * @description Register new user endpoint.
    */
    public function __invoke(RegisterNewUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $newUser = $this->registerUser($validatedData);

        $device = substr($request->userAgent() ?? '', 0, 255);
        $userTokenForDevice = $newUser->createToken($device)->plainTextToken;

        return response()->json([
            'access_token' => $userTokenForDevice,
        ], Response::HTTP_CREATED);

    }

    protected function registerUser(array $validatedData): User
    {
        $newUser = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $this->callEventNewUserRegistered($newUser);

        return $newUser;
    }

    protected function callEventNewUserRegistered(User $newUser): void
    {
        event(new Registered($newUser));
    }
}
