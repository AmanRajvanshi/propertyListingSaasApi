<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCreateListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCreateListingController extends Controller
{
  public function store(Request $request): JsonResponse
  {
    $request->validate(
      [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'message' => 'nullable|string',
        'property_type' => 'required|string|max:255',
      ]
    );

    $listing = UserCreateListing::create([
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
      'message' => $request->message,
      'property_type' => $request->property_type,
      'status'  => 'pending', // hardcoded
    ]);

    return response()->json([
      'status' => true,
      'message' => 'Listing submitted successfully.',
      'data' => $listing,
    ], 201);
  }

  public function index(): JsonResponse
  {
    $listings = UserCreateListing::latest()->get();

    if ($listings->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No enquiries found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $listings,
    ]);
  }

  public function updateStatus(Request $request, $id): JsonResponse
  {
    $request->validate([
      'status' => 'required|in:pending,responded,resolved,reopened,closed',
    ]);
    
    $listing = UserCreateListing::find($id);
    $listing->status = $request->status;
    $listing->save();

    return response()->json([
      'status' => true,
      'message' => 'Listing status updated successfully.',
      'data' => $listing,
    ]);
  }
}
