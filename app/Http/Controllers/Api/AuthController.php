<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  /**
   * Login user and issue Passport access token
   */
  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|string',
    ]);

    // Use web guard explicitly
    if (!Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
      return response()->json([
        'status' => false,
        'statusCode' => 401,
        'message' => 'Unauthorized',
      ], 401);
    }

    $user = Auth::guard('web')->user();
    $token = $user->createToken('User Access Token')->accessToken;

    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'Login successful.',
      'token' => $token,
      'user' => $user,
    ]);
  }

  /**
   * Logout user and revoke current access token
   */
  public function logout(Request $request): JsonResponse
  {
    // Revoke only current token
    $request->user()->token()->revoke();

    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'Logged out successfully.',
    ], 200);
  }
}
