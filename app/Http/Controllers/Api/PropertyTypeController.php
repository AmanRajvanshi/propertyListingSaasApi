<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Properties;
use App\Models\PropertyType;
use App\Models\PropertyTypeCity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyTypeController extends Controller
{
  // ✅ View all property types
  public function index(): JsonResponse
  {
    $types = PropertyType::all();

    if ($types->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No property types found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $types,
    ]);
  }

  // ➕ Add new property type
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|unique:property_types,name',
    ]);

    $type = PropertyType::create(['name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Property type added successfully.',
      'data' => $type,
    ], 201);
  }

  // ✏️ Edit property type
  public function update(Request $request, $id): JsonResponse
  {
    $type = PropertyType::findOrFail($id);

    $request->validate([
      'name' => 'required|string|unique:property_types,name,' . $type->id,
    ]);

    $type->update(['name' => $request->name]);

    return response()->json([
      'status' => true,
      'message' => 'Property type updated successfully.',
      'data' => $type,
    ]);
  }

  // ❌ Delete property type
  public function destroy($id): JsonResponse
  {
    $type = PropertyType::findOrFail($id);
    $type->delete();

    return response()->json([
      'status' => true,
      'message' => 'Property type deleted successfully.',
    ]);
  }

  // get property_count by type with property name
  public function getAllPropertyTypesWithCount(): JsonResponse
  {
    $types = PropertyType::withCount('properties')->get();

    if ($types->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No property types found.',
        'data' => [],
      ], 404);
    }

    $data = $types->map(function ($type) {
      return [
        'type_id' => $type->id,
        'type_name' => $type->name,
        'property_count' => $type->properties_count,
      ];
    });

    return response()->json([
      'status' => true,
      'message' => 'Property types with counts retrieved successfully.',
      'data' => $data,
    ], 200);
  }
}
