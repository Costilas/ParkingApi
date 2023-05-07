<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function __invoke(LoginUserRequest $request)
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
