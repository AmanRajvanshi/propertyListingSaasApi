<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonials;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
  // ✅ List all testimonials
  public function index(): JsonResponse
  {
    $testimonials = Testimonials::latest()->get();

    if ($testimonials->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No testimonials found.',
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $testimonials,
    ]);
  }

  // ➕ Add new testimonial
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'required|string',
      'image' => 'nullable|image|max:2048',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
      $imagePath = ImageHelper::compressAndStore($request->file('image'), 'testimonials');
    }

    $testimonial = Testimonials::create([
      'name' => $request->name,
      'description' => $request->description,
      'image' => $imagePath,
    ]);

    return response()->json([
      'status' => true,
      'message' => 'Testimonial created successfully.',
      'data' => $testimonial,
    ], 201);
  }

  // ✏️ Update testimonial
  public function update(Request $request, $id): JsonResponse
  {
    $testimonial = Testimonials::findOrFail($id);

    $request->validate([
      'name' => 'sometimes|required|string|max:255',
      'description' => 'sometimes|required|string',
      'image' => 'nullable|image|max:2048',
    ]);

    $updateData = [
      'name' => $request->name ?? $testimonial->name,
      'description' => $request->description ?? $testimonial->description,
      'image' => $testimonial->image
    ];

    if ($request->hasFile('image')) {
      \Log::info('Image file received:', [
        'filename' => $request->file('image')->getClientOriginalName()
      ]);

      if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
        Storage::disk('public')->delete($testimonial->image);
      }

      $imagePath = ImageHelper::compressAndStore($request->file('image'), 'testimonials');

      \Log::info('Image stored at path:', ['path' => $imagePath]);

      // Only set 'image' key if new image uploaded successfully
      if ($imagePath) {
        $updateData['image'] = $imagePath;
      }
    }

    \Log::info('Final update payload:', $updateData);

    $testimonial->update($updateData);

    return response()->json([
      'status' => true,
      'message' => 'Testimonial updated successfully.',
      'data' => $testimonial->refresh(),
    ]);
  }

  // ❌ Delete testimonial
  public function destroy($id): JsonResponse
  {
    $testimonial = Testimonials::findOrFail($id);

    if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
      Storage::disk('public')->delete($testimonial->image);
    }

    $testimonial->delete();

    return response()->json([
      'status' => true,
      'message' => 'Testimonial deleted successfully.',
    ]);
  }
}
