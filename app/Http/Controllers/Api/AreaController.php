<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
  // ✅ Get all areas
  public function index(): JsonResponse
  {
    $areas = Area::with(['city', 'state'])->paginate(20);

    if ($areas->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No areas found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $areas,
    ]);
  }

  // ➕ Add new area
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'area_name' => 'required|string',
      'city_id' => 'required|exists:cities,id',
      'state_id' => 'required|exists:states,id',
    ]);

    $area = Area::create([
      'area_name' => $request->area_name,
      'city_id' => $request->city_id,
      'state_id' => $request->state_id,
    ]);

    // Eager load the city and state relations
    $area->load('city', 'state');

    return response()->json([
      'status' => true,
      'message' => 'Area added successfully.',
      'data' => $area,
    ], 201);
  }

  // ✏️ Update area
  public function update(Request $request, $id): JsonResponse
  {
    $area = Area::findOrFail($id);

    $request->validate([
      'area_name' => 'required|string',
      'city_id' => 'required|exists:cities,id',
      'state_id' => 'required|exists:states,id',
    ]);

    $area->update([
      'area_name' => $request->area_name,
      'city_id' => $request->city_id,
      'state_id' => $request->state_id,
    ]);

    // Eager load the city and state relations
    $area->load('city', 'state');

    return response()->json([
      'status' => true,
      'message' => 'Area updated successfully.',
      'data' => $area,
    ]);
  }

  // ❌ Delete area
  public function destroy($id): JsonResponse
  {
    $area = Area::findOrFail($id);
    $area->delete();

    return response()->json([
      'status' => true,
      'message' => 'Area deleted successfully.',
    ]);
  }

  // Get areas by city
  public function getAreasByCity($cityId): JsonResponse
  {
    $areas = Area::where('city_id', $cityId)->get();

    return response()->json([
      'status' => true,
      'data' => $areas,
    ]);
  }

  // search city with area
  public function searchArea(Request $request): JsonResponse
  {
    $search = $request->input('search');

    if (!$search) {
      return response()->json([
        'status' => false,
        'message' => 'Search parameter is required.',
        'data' => [],
      ], 400);
    }

    $areas = Area::with(['city', 'state'])->where('area_name', 'like', "%$search%")->get();

    if ($areas->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No areas found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $areas,
    ]);
  }
}
