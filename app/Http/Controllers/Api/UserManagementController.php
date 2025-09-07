<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class UserManagementController extends Controller
{
  /**
   * Get all user profile
   */
  public function index(): JsonResponse
  {
    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'Users retrieved successfully.',
      'data' => User::all(),
    ]);
  }

  /**
   * Add a new user (admin only)
   */
  public function store(Request $request): JsonResponse
  {
    $authUser = auth()->user();

    if ($authUser->type !== 'admin') {
      return response()->json([
        'status' => false,
        'statusCode' => 403,
        'message' => 'Forbidden: Only admin can add users.',
      ], 403);
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|string|min:6',
      'routes' => 'nullable|array', // Accept an array of route names
      'type' => 'required',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'type' => $request->type,
      'routes' => $request->routes ?? [],
      'email_verified_at' => now(),
    ]);

    return response()->json([
      'status' => true,
      'statusCode' => 201,
      'message' => 'User created successfully.',
      'data' => $user,
    ]);
  }

  /**
   * Update user
   * - Admin can edit any user (name, email, password) — not type
   * - Regular users can only update their own password
   */
  public function update(Request $request, $id): JsonResponse
  {
    $authUser = auth()->user();
    $userToUpdate = User::findOrFail($id);

    // Only admin can update users other than themselves
    if ($authUser->type !== 'admin' && $authUser->id !== $userToUpdate->id) {
      return response()->json([
        'status'    => false,
        'statusCode' => 403,
        'message'   => 'You can only update your own password.',
      ], 403);
    }

    // Set up validation rules
    $rules = [];

    if ($authUser->type === 'admin') {
      $rules = [
        'name'     => 'sometimes|required|string|max:255',
        'email'    => 'sometimes|required|email|unique:users,email,' . $userToUpdate->id,
        'password' => 'sometimes|nullable|string|min:6',
        'type'     => 'sometimes|string|in:admin,manager,support,editor',
        'routes'   => 'sometimes|nullable|array',
      ];
    } else {
      $rules = [
        'password' => 'required|string|min:6',
      ];
    }

    $request->validate($rules);

    // Only one user can be admin
    if ($request->has('type') && $request->type === 'admin') {
      // Check if any other admin exists besides the current user (if this user is not already admin)
      if ($userToUpdate->type !== 'admin' && User::where('type', 'admin')->where('id', '!=', $userToUpdate->id)->exists()) {
        return response()->json([
          'status'    => false,
          'statusCode' => 400,
          'message'   => 'Only one admin is allowed.',
        ], 400);
      }
    }

    // Build the update data
    $updateData = [];

    if ($authUser->type === 'admin') {
      if ($request->filled('name'))     $updateData['name'] = $request->name;
      if ($request->filled('email'))    $updateData['email'] = $request->email;
      if ($request->filled('password')) $updateData['password'] = Hash::make($request->password);
      if ($request->has('type')) {
        $updateData['type'] = $request->type;
      }

      // For admins, always set routes to ["everything"]
      $isAdmin = ($request->has('type') && $request->type === 'admin') || $userToUpdate->type === 'admin';
      if ($isAdmin) {
        $updateData['routes'] = ["everything"];
      } else {
        // Non-admin: use the routes provided in the request (if any)
        if ($request->has('routes')) {
          $updateData['routes'] = $request->routes;
        }
      }
    } else {
      // Regular user can only update their own password
      $updateData['password'] = Hash::make($request->password);
    }

    $userToUpdate->update($updateData);

    return response()->json([
      'status'    => true,
      'statusCode' => 200,
      'message'   => 'User updated successfully.',
      'data'      => $userToUpdate,
    ]);
  }

  /**
   * Delete user (admin only, and cannot delete admin)
   */
  public function destroy($id): JsonResponse
  {
    $authUser = auth()->user();
    $user = User::findOrFail($id);

    if ($authUser->type !== 'admin') {
      return response()->json([
        'status' => false,
        'statusCode' => 403,
        'message' => 'Forbidden: Only admin can delete users.',
      ], 403);
    }

    if ($user->type === 'admin') {
      return response()->json([
        'status' => false,
        'statusCode' => 403,
        'message' => 'Admin user cannot be deleted.',
      ], 403);
    }

    $user->delete();

    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'User deleted successfully.',
    ]);
  }

  /**
   * Get authenticated user profile
   */
  public function profile(): JsonResponse
  {
    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'Profile retrieved successfully.',
      'data' => auth()->user(),
    ]);
  }

  // update password
  public function changePassword(Request $request, $userId): JsonResponse
  {
    // Extract the user requesting the change from the token
    $requestUser = auth()->user();

    // Verify the token belongs to the user whose password is being changed
    if ($requestUser->id != $userId) {
      return response()->json([
        'status' => false,
        'statusCode' => 403,
        'message' => 'You can only change your own password.',
      ], 403);
    }

    // Validate input
    $request->validate([
      'password' => 'required|string|min:6|confirmed',
    ]);

    // Update the password
    $user = User::findOrFail($userId);
    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json([
      'status' => true,
      'statusCode' => 200,
      'message' => 'Password updated successfully.',
      'data' => $user,
    ]);
  }
}
