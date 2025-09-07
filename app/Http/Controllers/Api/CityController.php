<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CityController extends Controller
{
  // ✅ Get all cities
  public function index(Request $request): JsonResponse
  {
    $paginate = (int) $request->query('paginate', 20);
    $cities = $paginate === 0
      ? City::with('state')->get()
      : City::with('state')->paginate($paginate);

    if ($cities->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No cities found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $cities,
    ]);
  }

  // ➕ Add new city
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'city_name' => 'required|string',
      'state_id' => 'required|exists:states,id',
      'status' => 'required|in:active,coming-soon',
      'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
      'is_main' => 'nullable|in:0,1', // Better validation for is_main
    ]);

    $slug = \Str::slug($request->city_name);

    $imagePath = null;
    if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('cities', 'public');
    }

    $city = City::create([
      'city_name' => $request->city_name,
      'state_id' => $request->state_id,
      'status' => $request->status,
      'image' => $imagePath,
      'is_main' => $request->is_main ? 1 : 0, // Explicit conversion
      'slug' => $slug,
    ]);

    // Eager load the state relation
    $city->load('state');

    return response()->json([
      'status' => true,
      'message' => 'City added successfully.',
      'data' => $city,
    ], 201);
  }

  // ✏️ Update city
  public function update(Request $request, $id): JsonResponse
  {
    $request->validate([
      'city_name' => 'required|string',
      'state_id' => 'required|exists:states,id',
      'status' => 'required|in:active,coming-soon',
      'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
      'is_main' => 'nullable|in:0,1', // Better validation for is_main
    ]);

    $slug = \Str::slug($request->city_name);
    $city = City::findOrFail($id);

    if ($request->hasFile('image') && $city->image) {
      Storage::disk('public')->delete($city->image);
    }

    $imagePath = $city->image;
    if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('cities', 'public');
    }

    $city->update([
      'city_name' => $request->city_name,
      'state_id' => $request->state_id,
      'status' => $request->status,
      'image' => $imagePath,
      'is_main' => $request->is_main ? 1 : 0, // Explicit conversion
      'slug' => $slug,
    ]);

    // Eager load the state relation
    $city->load('state');

    return response()->json([
      'status' => true,
      'message' => 'City updated successfully.',
      'data' => $city,
    ]);
  }

  // ❌ Delete city
  public function destroy($id): JsonResponse
  {
    $city = City::findOrFail($id);

    if ($city->image) {
      Storage::disk('public')->delete($city->image);
    }

    $city->delete();

    return response()->json([
      'status' => true,
      'message' => 'City deleted successfully.',
    ]);
  }

  public function getCitiesByState($stateId): JsonResponse
  {
    if ($stateId == 0) {
      $cities = City::all();
    } else {
      $cities = City::where('state_id', $stateId)->get();
    }

    if ($cities->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No cities found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $cities,
    ]);
  }

  // search city with state
  public function searchCity(Request $request): JsonResponse
  {
    $search = $request->input('search');

    if (!$search) {
      return response()->json([
        'status' => false,
        'message' => 'Search parameter is required.',
        'data' => [],
      ], 400);
    }

    $cities = City::with('state')
      ->where('city_name', 'like', "%$search%")
      ->get();

    if ($cities->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No cities found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $cities,
    ]);
  }
}
