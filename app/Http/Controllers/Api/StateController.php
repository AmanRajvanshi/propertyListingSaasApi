<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StateController extends Controller
{
  // ✅ View all states
  public function index(): JsonResponse
  {
    $states = State::paginate(20);

    if ($states->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No states found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $states,
    ]);
  }

  // ➕ Add new state
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'state_name' => 'required|string|unique:states,state_name',
    ]);

    $state = State::create(['state_name' => $request->state_name]);

    return response()->json([
      'status' => true,
      'message' => 'State added successfully.',
      'data' => $state,
    ], 201);
  }

  // ✏️ Edit state
  public function update(Request $request, $id): JsonResponse
  {
    $state = State::findOrFail($id);

    $request->validate([
      'state_name' => 'required|string|unique:states,state_name,' . $state->id,
    ]);

    $state->update(['state_name' => $request->state_name]);

    return response()->json([
      'status' => true,
      'message' => 'State updated successfully.',
      'data' => $state,
    ]);
  }

  // ❌ Delete state
  public function destroy($id): JsonResponse
  {
    $state = State::findOrFail($id);
    $state->delete();

    return response()->json([
      'status' => true,
      'message' => 'State deleted successfully.',
    ]);
  }

  // search city with state
  public function searchState(Request $request): JsonResponse
  {
    $search = $request->input('search');

    if (!$search) {
      return response()->json([
        'status' => false,
        'message' => 'Search parameter is required.',
        'data' => [],
      ], 400);
    }

    $states = State::where('state_name', 'like', "%$search%")->get();

    if ($states->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No states found.',
        'data' => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $states,
    ]);
  }
}
