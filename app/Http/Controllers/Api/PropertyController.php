<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertyType;
use App\Models\PropertyTypeCity;
use App\Models\MultiplePricingsOfProperty; // Add this model
use Illuminate\Support\Str;

class PropertyController extends Controller
{
  public function store(Request $request)
  {
    $validated = $request->validate([
      'property_title'           => 'required|string|max:255',
      'property_description'     => 'required|string',
      'property_street_address'  => 'sometimes|nullable|string',
      'state_id'                 => 'required|integer',
      'city_id'                  => 'required|integer',
      'area_id'                  => 'required|integer',
      'property_type'            => 'required|integer',
      'property_rent'            => 'required_without:has_multiple_pricing|nullable|numeric',
      'property_rent_frequency'  => 'required_without:has_multiple_pricing|nullable|string',
      'sharing_type'             => 'required_without:has_multiple_pricing|nullable|string',
      'occupancy_type'           => 'required_without:has_multiple_pricing|nullable|string',
      'has_multiple_pricing'     => 'sometimes|boolean',
      'multiple_pricings'        => 'required_if:has_multiple_pricing,true|array',
      'multiple_pricings.*.property_rent' => 'required_if:has_multiple_pricing,true|numeric',
      'multiple_pricings.*.property_rent_frequency' => 'required_if:has_multiple_pricing,true|string',
      'multiple_pricings.*.sharing_type' => 'required_if:has_multiple_pricing,true|string',
      'multiple_pricings.*.occupancy_type' => 'required_if:has_multiple_pricing,true|string',
      'no_of_rooms'              => 'sometimes|nullable|integer',
      'no_of_bathrooms'          => 'sometimes|nullable|integer',
      'year_built'               => 'sometimes|nullable|integer',
      'map'                      => 'required|string',
      'status'                   => 'required|string',
      'is_property_favourite'    => 'sometimes|nullable|boolean',
      'meta_title'               => 'required|string|max:255',
      'meta_keywords'            => 'required|array',
      'meta_keywords.*'          => 'string',
      'meta_description'         => 'required|string',
      'amenities'                => 'required|array',
      'amenities.*'              => 'integer|exists:amenities,id',
      'nearby_locations'         => 'required|array',
      'nearby_locations.*'       => 'integer|exists:nearby_locations,id',
      'images'                   => 'sometimes|array',
      'images.*'                 => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // Handle meta_keywords as JSON
    $validated['meta_keywords'] = json_encode($validated['meta_keywords']);

    // **FIX: Explicitly handle has_multiple_pricing**
    $validated['has_multiple_pricing'] = $request->has('has_multiple_pricing') && $request->has_multiple_pricing ? 1 : 0;

    // Generate slug
    $slug = Str::slug($validated['property_title']) . '-' . strtolower(Str::random(15));
    $property = Properties::create(array_merge($validated, ['slug' => $slug]));

    // Handle multiple pricing if enabled
    if ($validated['has_multiple_pricing'] && $request->has('multiple_pricings')) {
      foreach ($request->multiple_pricings as $pricing) {
        MultiplePricingsOfProperty::create([
          'property_id' => $property->id,
          'property_rent' => $pricing['property_rent'],
          'property_rent_frequency' => $pricing['property_rent_frequency'],
          'sharing_type' => $pricing['sharing_type'],
          'occupancy_type' => $pricing['occupancy_type'],
        ]);
      }
    }

    // Attach amenities and nearby locations
    $property->amenities()->sync($request->amenities);
    $property->nearbyLocations()->sync($request->nearby_locations);

    // Store property images if any
    if ($request->hasFile('images')) {
      $mainImageIndex = $request->input('is_main', 0);
      $files = $request->file('images');
      if (!is_array($files)) $files = [$files];
      foreach ($files as $idx => $file) {
        $compressedPath = ImageHelper::compressAndStore($file, 'property_images');
        $randomString = Str::random(5);
        $alt_text = $randomString . '_' . $file->getClientOriginalName();
        \App\Models\PropertyImage::create([
          'property_id' => $property->id,
          'image_path'  => $compressedPath,
          'alt_text'    => $alt_text,
          'is_main'     => ($idx == $mainImageIndex) ? 1 : 0
        ]);
      }
    }

    // Insert a record in property_type_city
    PropertyTypeCity::create([
      'property_id'      => $property->id,
      'property_type_id' => $property->property_type,
      'city_id'          => $property->city_id,
    ]);

    // Load relationships and decode meta_keywords for response
    $property->load(['amenities', 'nearbyLocations', 'images', 'multiplePricings']);
    $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];

    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Property created successfully!',
    ], 201);
  }

  public function update(Request $request, $id)
  {
    $property = Properties::findOrFail($id);

    $validated = $request->validate([
      'property_title'           => 'required|string|max:255',
      'property_description'     => 'required|string',
      'property_street_address'  => 'sometimes|nullable|string',
      'state_id'                 => 'required|integer',
      'city_id'                  => 'required|integer',
      'area_id'                  => 'required|integer',
      'property_type'            => 'required|integer',
      'property_rent'            => 'required_without:has_multiple_pricing|nullable|numeric',
      'property_rent_frequency'  => 'required_without:has_multiple_pricing|nullable|string',
      'sharing_type'             => 'required_without:has_multiple_pricing|nullable|string',
      'occupancy_type'           => 'required_without:has_multiple_pricing|nullable|string',
      'has_multiple_pricing'     => 'sometimes|boolean',
      'multiple_pricings'        => 'required_if:has_multiple_pricing,true|array',
      'multiple_pricings.*.property_rent' => 'required_if:has_multiple_pricing,true|numeric',
      'multiple_pricings.*.property_rent_frequency' => 'required_if:has_multiple_pricing,true|string',
      'multiple_pricings.*.sharing_type' => 'required_if:has_multiple_pricing,true|string',
      'multiple_pricings.*.occupancy_type' => 'required_if:has_multiple_pricing,true|string',
      'no_of_rooms'              => 'sometimes|nullable|integer',
      'no_of_bathrooms'          => 'sometimes|nullable|integer',
      'year_built'               => 'sometimes|nullable|integer',
      'map'                      => 'required|string',
      'status'                   => 'required|string',
      'is_property_favourite'    => 'sometimes|nullable|boolean',
      'meta_title'               => 'required|string|max:255',
      'meta_keywords'            => 'required|array',
      'meta_keywords.*'          => 'string',
      'meta_description'         => 'required|string',
      'amenities'                => 'required|array',
      'amenities.*'              => 'integer|exists:amenities,id',
      'nearby_locations'         => 'required|array',
      'nearby_locations.*'       => 'integer|exists:nearby_locations,id',
      'images'                   => 'sometimes|array',
      'images.*'                 => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // Handle meta_keywords as JSON
    $validated['meta_keywords'] = json_encode($validated['meta_keywords']);

    // **FIX: Explicitly handle has_multiple_pricing**
    $validated['has_multiple_pricing'] = $request->has('has_multiple_pricing') && $request->has_multiple_pricing ? 1 : 0;

    // Update core property fields
    $property->update($validated);

    // Handle multiple pricing updates
    if ($request->has('has_multiple_pricing')) {
      if ($request->has_multiple_pricing) {
        // Clear existing multiple pricing records
        MultiplePricingsOfProperty::where('property_id', $property->id)->delete();

        // Insert new multiple pricing records
        if ($request->has('multiple_pricings')) {
          foreach ($request->multiple_pricings as $pricing) {
            MultiplePricingsOfProperty::create([
              'property_id' => $property->id,
              'property_rent' => $pricing['property_rent'],
              'property_rent_frequency' => $pricing['property_rent_frequency'],
              'sharing_type' => $pricing['sharing_type'],
              'occupancy_type' => $pricing['occupancy_type'],
            ]);
          }
        }
      } else {
        // If has_multiple_pricing is false, clear all multiple pricing records
        MultiplePricingsOfProperty::where('property_id', $property->id)->delete();
      }
    }

    // Sync amenities/nearby
    $property->amenities()->sync($request->input('amenities', []));
    $property->nearbyLocations()->sync($request->input('nearby_locations', []));

    // ---------- IMAGE MANAGEMENT ----------
    // 1. Remove DB images not present in existing_images[]
    $keepImageIds = $request->input('existing_images', []);
    $currentIds = $property->images()->pluck('id')->toArray();
    $toDelete = array_diff($currentIds, $keepImageIds);
    if ($toDelete) {
      \App\Models\PropertyImage::destroy($toDelete);
    }
    // 2. Recompose the ordered list: kept (DB) images in order, then new images in UI/app order.
    $orderedImages = [];
    // First, DB (kept) images in the order sent from form
    foreach ($keepImageIds as $imgId) {
      $img = \App\Models\PropertyImage::find($imgId);
      if ($img) {
        $img->is_main = 0;
        $img->save();
        $orderedImages[] = $img;
      }
    }
    // 3. Save new images and add to ordered list
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $file) {
        $compressedPath = ImageHelper::compressAndStore($file, 'property_images');
        $alt_text = \Illuminate\Support\Str::random(5) . '_' . $file->getClientOriginalName();
        $newImg = \App\Models\PropertyImage::create([
          'property_id' => $property->id,
          'image_path'  => $compressedPath,
          'alt_text'    => $alt_text,
          'is_main'     => 0,
        ]);
        $orderedImages[] = $newImg;
      }
    }
    // 4. Set the right main image based on main_image_index (0-based in orderedImages)
    if (isset($orderedImages[$request->main_image_index])) {
      $mainImg = $orderedImages[$request->main_image_index];
      $mainImg->is_main = 1;
      $mainImg->save();
    }
    // ---------- /IMAGE MANAGEMENT ----------

    // Insert or update a record in property_type_city (per your business logic)
    PropertyTypeCity::updateOrCreate([
      'property_id'      => $property->id,
      'property_type_id' => $property->property_type ?? $validated['property_type'],
      'city_id'          => $property->city_id ?? $validated['city_id'],
    ]);

    // Load relationships and decode meta_keywords for response
    $property->load(['amenities', 'nearbyLocations', 'images', 'multiplePricings']);
    $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];

    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Property updated successfully!',
    ], 200);
  }

  public function show($slug)
  {
    $property = Properties::with([
      'amenities',
      'nearbyLocations',
      'images',
      'state',
      'city',
      'area',
      'typeCityLinks',
      'multiplePricings'
    ])->where('slug', $slug)->first();

    if (!$property) {
      return response()->json([
        'success' => false,
        'message' => 'Property not found.',
      ], 404);
    }

    // Decode meta_keywords after loading
    $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];

    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Property found successfully!',
    ], 200);
  }

  public function mark_as_favourite(Request $request, $id)
  {
    $property = Properties::findOrFail($id);

    $property->is_property_favourite = $request->is_property_favourite;
    $property->save();

    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Property updated successfully!',
    ], 200);
  }

  public function mark_as_deleted($id)
  {
    $property = Properties::findOrFail($id);

    $property->status = 'deleted';
    $property->save();

    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Property updated successfully!',
    ], 200);
  }

  public function index(Request $request)
  {
    $query = Properties::with([
      'amenities',
      'nearbyLocations',
      'images',
      'state',
      'city',
      'area',
      'typeCityLinks',
      'typeCityLinks.propertyType',
      'multiplePricings'
    ]);

    // SEARCH
    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('property_title', 'like', "%$search%");
      });
    }

    // FILTER BY STATUS
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // FILTER BY CITY
    if ($request->filled('city_id')) {
      $query->where('city_id', $request->city_id);
    }

    // FILTER BY PROPERTY TYPE
    if ($request->filled('property_type')) {
      $query->where('property_type', $request->property_type);
    }

    // FILTER BY PRICE RANGE - Only check main table
    if ($request->filled('price_range')) {
      $query->whereBetween('property_rent', $request->price_range);
    }

    // PAGINATION
    $perPage = $request->get('per_page', 5);
    $properties = $query->orderBy('created_at', 'desc')->paginate($perPage);

    // Decode meta_keywords for each property in the paginated results
    $properties->getCollection()->each(function ($property) {
      $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];
    });

    return response()->json($properties);
  }

  public function showWeb($slug)
  {
    $property = Properties::with([
      'amenities',
      'nearbyLocations',
      'images',
      'state',
      'city',
      'area',
      'propertyType',
      'typeCityLinks',
      'multiplePricings'
    ])->where('slug', $slug)->first();

    if (!$property) {
      return response()->json([
        'status' => false,
        'message' => 'Property not found.',
      ], 404);
    }

    // Decode meta_keywords for main property
    $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];

    // Get related properties - same type or same city
    $relatedProperties = Properties::with(['images', 'city', 'area', 'state', 'propertyType', 'amenities', 'multiplePricings'])
      ->where('id', '!=', $property->id)
      ->where(function ($query) use ($property) {
        $query->where('property_type', $property->property_type)
          ->orWhere('city_id', $property->city_id);
      })
      ->where('status', 'active')
      ->limit(4)
      ->get();

    // Decode meta_keywords for related properties
    $relatedProperties->each(function ($relatedProperty) {
      $relatedProperty->meta_keywords = json_decode($relatedProperty->meta_keywords, true) ?: [];
    });

    return response()->json([
      'status' => true,
      'property' => $property,
      'related_properties' => $relatedProperties,
      'message' => 'Property found successfully!',
    ], 200);
  }

  public function updateViews(Request $request, $slug)
  {
    $property = Properties::where('slug', $slug)->first();
    $property->increment('views');
    return response()->json([
      'success'  => true,
      'property' => $property,
      'message'  => 'Views updated successfully!',
    ], 200);
  }

  public function fetchByCityAndType(Request $request)
  {
    $cityId = (int) $request->query('city_id');
    $typeId = (int) $request->query('type_id');
    $areaId = (int) $request->query('area_id');
    $priceRange = $request->query('price_range');
    $sharingType = $request->query('sharing_type');
    $occupancyType = $request->query('occupancy_type');
    $perPage = (int) $request->query('per_page', 12);

    if (!$typeId) {
      return response()->json([
        'status' => false,
        'message' => 'type_id is required',
        'data' => [],
      ], 400);
    }

    $perPage = min($perPage, 50); // Max 50 items per page

    // Find related models
    $city = $cityId !== 0 ? City::find($cityId) : null;
    $propertyType = PropertyType::find($typeId);
    $area = $areaId ? Area::find($areaId) : null;

    // Validation
    if (!$propertyType) {
      return response()->json([
        'status' => false,
        'message' => 'Property type not found',
        'data' => [],
      ], 404);
    }

    if ($cityId !== 0 && !$city) {
      return response()->json([
        'status' => false,
        'message' => 'City not found',
        'data' => [],
      ], 404);
    }

    if ($areaId && !$area) {
      return response()->json([
        'status' => false,
        'message' => 'Area not found',
        'data' => [],
      ], 404);
    }

    // Get property IDs from pivot table
    $pivotQuery = PropertyTypeCity::where('property_type_id', $typeId);
    if ($cityId !== 0) {
      $pivotQuery->where('city_id', $cityId);
    }
    $propertyIds = $pivotQuery->pluck('property_id');

    if ($propertyIds->isEmpty()) {
      // Return empty paginated result
      $emptyPagination = new \Illuminate\Pagination\LengthAwarePaginator(
        [],
        0,
        $perPage,
        1,
        ['path' => request()->url(), 'pageName' => 'page']
      );

      return response()->json([
        'status' => true,
        'data' => $emptyPagination,
        'city' => $city,
        'area' => $area,
        'propertyType' => $propertyType,
      ]);
    }

    // Build main properties query
    $propertiesQuery = Properties::whereIn('id', $propertyIds)
      ->with(['amenities', 'nearbyLocations', 'images', 'city', 'state', 'area', 'propertyType', 'multiplePricings']);

    // Apply area filter
    if ($areaId) {
      $propertiesQuery->where('area_id', $areaId);
    }

    // FILTER BY PRICE RANGE - Only check main table
    if ($request->filled('price_range')) {
      $priceRangeParts = explode('-', $priceRange);
      if (count($priceRangeParts) === 2) {
        $minPrice = (int) $priceRangeParts[0];
        $maxPrice = $priceRangeParts[1] === '' ? PHP_INT_MAX : (int) $priceRangeParts[1];

        $propertiesQuery->whereBetween('property_rent', [$minPrice, $maxPrice]);
      }
    }

    // FILTER BY SHARING TYPE - Only check main table
    if ($request->filled('sharing_type')) {
      $propertiesQuery->where('sharing_type', $sharingType);
    }

    // FILTER BY OCCUPANCY TYPE - Only check main table
    if ($request->filled('occupancy_type')) {
      $propertiesQuery->where('occupancy_type', $occupancyType);
    }

    // Order and paginate using Laravel's built-in pagination
    $propertiesQuery->orderBy('created_at', 'desc');
    $paginatedProperties = $propertiesQuery->paginate($perPage);

    // Decode meta_keywords for each property in pagination results
    $paginatedProperties->getCollection()->each(function ($property) {
      $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];
    });

    return response()->json([
      'status' => true,
      'data' => $paginatedProperties,
      'city' => $city,
      'area' => $area,
      'propertyType' => $propertyType,
      'applied_filters' => [
        'type_id' => $typeId,
        'city_id' => $cityId,
        'area_id' => $areaId ?: null,
        'price_range' => $priceRange ?: null,
        'sharing_type' => $sharingType ?: null,
        'occupancy_type' => $occupancyType ?: null,
      ]
    ]);
  }

  public function getFeaturedProperties()
  {
    $properties = Properties::where('is_property_favourite', 1)
      ->with(['amenities', 'nearbyLocations', 'images', 'state', 'city', 'area', 'propertyType', 'multiplePricings'])
      ->get();

    // Decode meta_keywords for each featured property
    $properties->each(function ($property) {
      $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];
    });

    return response()->json([
      'status' => true,
      'data' => $properties
    ]);
  }

  public function getTopProperties()
  {
    $properties = Properties::orderBy('views', 'desc')
      ->with(['amenities', 'images', 'state', 'city', 'area', 'multiplePricings', 'propertyType'])
      ->limit(3)
      ->get();

    // Decode meta_keywords for each top property
    $properties->each(function ($property) {
      $property->meta_keywords = json_decode($property->meta_keywords, true) ?: [];
    });

    return response()->json([
      'status' => true,
      'data' => $properties,
      'message' => 'Properties fetched successfully'
    ]);
  }
}
