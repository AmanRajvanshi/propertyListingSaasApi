<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CounterController extends Controller
{
  // ✅ Get all counters
  public function index(): JsonResponse
  {
    $counters = Counter::all();

    if ($counters->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No counters found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $counters,
    ]);
  }

  // ➕ Add new counter
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'counter_title' => 'required|string',
      'count' => 'required|integer|min:0',
    ]);

    $counter = Counter::create($request->only(['counter_title', 'count']));

    return response()->json([
      'status' => true,
      'message' => 'Counter added successfully.',
      'data' => $counter,
    ], 201);
  }

  // ✏️ Update counter
  public function update(Request $request, $id): JsonResponse
  {
    $counter = Counter::findOrFail($id);

    $request->validate([
      'counter_title' => 'required|string',
      'count' => 'required|integer|min:0',
    ]);

    $counter->update($request->only(['counter_title', 'count']));

    return response()->json([
      'status' => true,
      'message' => 'Counter updated successfully.',
      'data' => $counter,
    ]);
  }

  // ❌ Delete counter
  public function destroy($id): JsonResponse
  {
    $counter = Counter::findOrFail($id);
    $counter->delete();

    return response()->json([
      'status' => true,
      'message' => 'Counter deleted successfully.',
    ]);
  }
}
