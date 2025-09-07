<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyEnquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyEnquiryController extends Controller
{
  // ➕ Store property enquiry
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'name'        => 'required|string|max:255',
      'email'       => 'required|email|max:255',
      'phone'       => 'required|string|max:20',
      'message'     => 'nullable|string',
      'property_id' => 'required|exists:properties,id',
    ]);

    $enquiry = PropertyEnquiry::create([
      'name'        => $request->name,
      'email'       => $request->email,
      'phone'       => $request->phone,
      'message'     => $request->message,
      'property_id' => $request->property_id,
      'status'      => 'pending',
    ]);

    return response()->json([
      'status'  => true,
      'message' => 'Property enquiry submitted successfully.',
      'data'    => $enquiry,
    ], 201);
  }

  // 📥 Get all enquiries
  public function index(): JsonResponse
  {
    $enquiries = PropertyEnquiry::with('property')->latest()->get();

    if ($enquiries->isEmpty()) {
      return response()->json([
        'status'  => false,
        'message' => 'No property enquiries found.',
        'data'    => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data'   => $enquiries,
    ]);
  }

  // ✏️ Update status
  public function updateStatus(Request $request, $id): JsonResponse
  {
    // Optional: manually validate $status if not using $request->validate()
    if (!in_array($status = $request->status, ['pending', 'responded', 'resolved', 'reopened', 'closed'])) {
      return response()->json([
        'status' => false,
        'message' => 'Invalid status value.',
      ], 400);
    }

    $enquiry = PropertyEnquiry::findOrFail($id);
    $enquiry->status = $status; // This is safe, Eloquent will quote it
    $enquiry->save();

    return response()->json([
      'status'  => true,
      'message' => 'Enquiry status updated.',
      'data'    => $enquiry,
    ]);
  }
}
