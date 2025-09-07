<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
  public function index(): JsonResponse
  {
    $blogs = Blog::latest()->where('status', 'active')->paginate(8);

    if ($blogs->isEmpty()) {
      return response()->json(['status' => false, 'message' => 'No blogs found.'], 404);
    }

    // Decode meta_keywords for each blog
    $blogs->each(function ($blog) {
      $blog->meta_keywords = json_decode($blog->meta_keywords, true) ?: [];
    });

    return response()->json(['status' => true, 'data' => $blogs]);
  }

  public function show($id): JsonResponse
  {
    $blog = Blog::find($id);

    if (!$blog) {
      return response()->json(['status' => false, 'message' => 'Blog not found.'], 404);
    }

    // Decode meta_keywords after loading
    $blog->meta_keywords = json_decode($blog->meta_keywords, true) ?: [];

    return response()->json(['status' => true, 'data' => $blog]);
  }

  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'title' => 'required|string',
      'description' => 'required|string',
      'image' => 'nullable|image|max:2048',
      'meta_title' => 'required|string|max:255',
      'meta_keywords' => 'required|array',
      'meta_keywords.*' => 'string',
      'meta_description' => 'required|string',
      'status' => 'nullable|in:draft,active',
    ]);

    // Handle meta_keywords as JSON (same as property controller)
    $validated['meta_keywords'] = json_encode($validated['meta_keywords']);

    // Handle image
    $imagePath = null;
    if ($request->hasFile('image')) {
      $imagePath = ImageHelper::compressAndStore($request->file('image'), 'blogs');
    }

    // Generate unique slug
    $slug = Str::slug($validated['title']);
    $originalSlug = $slug;
    $count = 1;
    while (Blog::where('slug', $slug)->exists()) {
      $slug = $originalSlug . '-' . $count++;
    }

    // Prepare blog data
    $blogData = array_merge($validated, [
      'image' => $imagePath,
      'views' => 0,
      'slug' => $slug,
      'status' => $request->input('status', 'active'),
    ]);

    $blog = Blog::create($blogData);

    // Decode meta_keywords for response
    $blog->meta_keywords = json_decode($blog->meta_keywords, true);

    return response()->json([
      'status' => true,
      'message' => 'Blog created successfully.',
      'data' => $blog
    ], 201);
  }

  public function update(Request $request, $id): JsonResponse
  {
    $validated = $request->validate([
      'title' => 'required|string',
      'description' => 'required|string',
      'image' => 'nullable|image|max:2048',
      'meta_title' => 'required|string|max:255',
      'meta_keywords' => 'required|array',
      'meta_keywords.*' => 'string',
      'meta_description' => 'required|string',
      'status' => 'nullable|in:draft,active',
    ]);

    $blog = Blog::findOrFail($id);

    // Handle meta_keywords as JSON (same as property controller)
    $validated['meta_keywords'] = json_encode($validated['meta_keywords']);

    // Handle image update
    $imagePath = $blog->image;
    if ($request->hasFile('image')) {
      if ($imagePath && Storage::disk('public')->exists($imagePath)) {
        Storage::disk('public')->delete($imagePath);
      }
      $imagePath = ImageHelper::compressAndStore($request->file('image'), 'blogs');
    }

    // Slug regeneration only if title changed
    if ($blog->title !== $validated['title']) {
      $slug = Str::slug($validated['title']);
      $originalSlug = $slug;
      $count = 1;
      while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
        $slug = $originalSlug . '-' . $count++;
      }
      $validated['slug'] = $slug;
    }

    $validated['image'] = $imagePath;

    $blog->update($validated);

    // Decode meta_keywords for response
    $blog->meta_keywords = json_decode($blog->meta_keywords, true);

    return response()->json([
      'status' => true,
      'message' => 'Blog updated successfully.',
      'data' => $blog
    ]);
  }

  public function destroy($id): JsonResponse
  {
    $blog = Blog::findOrFail($id);

    if ($blog->image && Storage::disk('public')->exists($blog->image)) {
      Storage::disk('public')->delete($blog->image);
    }

    $blog->delete();

    return response()->json([
      'status' => true,
      'message' => 'Blog deleted successfully.'
    ]);
  }

  public function singleBlog(Request $request, $id): JsonResponse
  {
    $blog = Blog::find($id);

    if (!$blog) {
      return response()->json(['status' => false, 'message' => 'Blog not found.'], 404);
    }

    // Decode meta_keywords after loading
    $blog->meta_keywords = json_decode($blog->meta_keywords, true) ?: [];

    return response()->json(['status' => true, 'data' => $blog]);
  }
}
