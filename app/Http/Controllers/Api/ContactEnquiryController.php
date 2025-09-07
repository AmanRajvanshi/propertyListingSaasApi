<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactEnquiryController extends Controller
{
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'phone' => 'nullable|string|max:20',
      'subject' => 'required|string|max:255',
      'message' => 'required|string',
    ]);

    $enquiry = ContactEnquiry::create([
      'name'    => $request->name,
      'email'   => $request->email,
      'phone'   => $request->phone,
      'subject' => $request->subject,
      'message' => $request->message,
      'status'  => 'pending', // hardcoded
    ]);

    return response()->json([
      'status' => true,
      'message' => 'Contact enquiry submitted successfully.',
      'data'    => $enquiry,
    ], 201);
  }

  public function index(): JsonResponse
  {
    $enquiries = ContactEnquiry::latest()->get();

    if ($enquiries->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No enquiries found.',
        'data'    => [],
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data'    => $enquiries,
    ]);
  }

  public function updateStatus(Request $request, $id): JsonResponse
  {
    $request->validate([
      'status' => 'required|in:pending,responded,resolved,reopened,closed',
    ]);

    $enquiry = ContactEnquiry::findOrFail($id);
    $enquiry->status = $request->status;
    $enquiry->save();

    return response()->json([
      'status' => true,
      'message' => 'Enquiry status updated successfully.',
      'data'    => $enquiry,
    ]);
  }
}
