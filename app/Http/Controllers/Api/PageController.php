<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PageController extends Controller
{
  public function index(): JsonResponse
  {
    $pages = Page::all();

    // if no pages found
    if ($pages->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'No pages found.',
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $pages,
    ]);
  }

  public function showBySlug($slug): JsonResponse
  {
    $page = Page::where('slug', $slug)->first();

    if (!$page) {
      return response()->json([
        'status' => false,
        'message' => 'Page not found.',
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => $page,
    ]);
  }

  public function storeOrUpdate(Request $request): JsonResponse
  {
    $request->validate([
      'title' => 'required|string',
      'content' => 'nullable|string',
      'status' => 'required|in:active,draft',
      'meta_title' => 'nullable|string|max:255',
      'meta_keywords' => 'nullable|array',
      'meta_description' => 'nullable|string',
      'slug' => 'required|string',
    ]);

    $slug = $request->slug ?? Str::slug($request->title);

    // Convert meta_keywords array to comma-separated string for storage
    $metaKeywords = null;
    if ($request->meta_keywords && is_array($request->meta_keywords)) {
      $metaKeywords = implode(',', array_filter($request->meta_keywords));
    } elseif ($request->meta_keywords && is_string($request->meta_keywords)) {
      $metaKeywords = $request->meta_keywords;
    }

    $page = Page::updateOrCreate(
      ['slug' => $slug],
      [
        'title' => $request->title,
        'slug' => $slug,
        'content' => $request->content,
        'status' => $request->status,
        'meta_title' => $request->meta_title,
        'meta_keywords' => $metaKeywords,
        'meta_description' => $request->meta_description,
      ]
    );

    // Convert meta_keywords back to array for response
    if ($page->meta_keywords) {
      $page->meta_keywords = array_filter(explode(',', $page->meta_keywords));
    }

    return response()->json([
      'status' => true,
      'message' => 'Page saved successfully.',
      'data' => $page,
    ]);
  }

  public function showFrontend($slug): JsonResponse
  {
    $page = Page::where('slug', $slug)
      ->where('status', 'active')
      ->first();

    if (!$page) {
      return response()->json([
        'status' => false,
        'message' => 'Page not found.',
      ], 404);
    }

    // Convert meta_keywords from string to array for frontend consistency
    if ($page->meta_keywords) {
      $page->meta_keywords = array_filter(explode(',', $page->meta_keywords));
    }

    return response()->json([
      'status' => true,
      'data' => $page,
    ]);
  }

  // give me all the pages which are active but only the slugs and title
  public function getActivePages(): JsonResponse
  {
    $pages = Page::where('status', 'active')->get(['slug', 'title']);
    return response()->json([
      'status' => true,
      'data' => $pages,
    ]);
  }
}
