<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AmenityController extends Controller
{
  // ✅ View all amenities
  public function index(): JsonResponse
  {

    $amenities = Amenity::all();

    if ($amenities->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No amenities found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $amenities,
    ], 200);
  }

  // ➕ Add new amenity
  public function store(Request $request): JsonResponse
  {

    $request->validate([
      'name' => 'required|string|unique:amenities,name',
    ]);

    $amenity = Amenity::create(['name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Amenity added successfully.',
      'data' => $amenity,
    ], 201);
  }

  // ✏️ Edit amenity
  public function update(Request $request, $id): JsonResponse
  {

    $amenity = Amenity::findOrFail($id);

    $request->validate([
      'name' => 'required|string|unique:amenities,name,' . $amenity->id,
    ]);

    $amenity->update(['name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Amenity updated successfully.',
      'data' => $amenity,
    ]);
  }

  // ❌ Delete amenity
  public function destroy($id): JsonResponse
  {

    $amenity = Amenity::findOrFail($id);
    $amenity->delete();

    return response()->json([
      'status' => true,
      'message' => 'Amenity deleted successfully.',
    ]);
  }
}
