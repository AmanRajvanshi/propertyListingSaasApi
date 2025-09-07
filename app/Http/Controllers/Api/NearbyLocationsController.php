<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NearbyLocation;
use App\Models\NearbyLocations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NearbyLocationsController extends Controller
{
  // view all nearby locations
  public function index(): JsonResponse{
    $nearbyLocations = NearbyLocation::all();

    if ($nearbyLocations->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No nearby locations found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $nearbyLocations,
    ], 200);
  }

  // ➕ Add new nearby location
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|unique:nearby_locations,nearby_location_name',
    ]);

    $nearbyLocation = NearbyLocation::create(['nearby_location_name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Nearby location added successfully.',
      'data' => $nearbyLocation,
    ], 201);
  }

  // ✏️ Edit nearby location
  public function update(Request $request, $id): JsonResponse
  {
    $nearbyLocation = NearbyLocation::findOrFail($id);

    $request->validate([
      'name' => 'required|string|unique:nearby_locations,nearby_location_name,' . $nearbyLocation->id,
    ]);

    $nearbyLocation->update(['nearby_location_name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Nearby location updated successfully.',
      'data' => $nearbyLocation,
    ]);
  }

  // ❌ Delete nearby location
  public function destroy($id): JsonResponse
  {
    $nearbyLocation = NearbyLocation::findOrFail($id);
    $nearbyLocation->delete();

    return response()->json([
      'status' => true,
      'message' => 'Nearby location deleted successfully.',
    ]);
  }
}
