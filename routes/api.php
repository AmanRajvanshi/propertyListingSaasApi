<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CompanyDetailsController;
use App\Http\Controllers\Api\ContactEnquiryController;
use App\Http\Controllers\Api\CounterController;
use App\Http\Controllers\Api\NearbyLocationsController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\PropertyEnquiryController;
use App\Http\Controllers\Api\PropertyTypeController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\UserCreateListingController;

/*
|--------------------------------------------------------------------------
| Website Routes (Public APIs)
|--------------------------------------------------------------------------
*/

Route::prefix('website')->group(function () {
  Route::post('/add-contact-enquiries', [ContactEnquiryController::class, 'store']);
  Route::post('/add-property-enquiries', [PropertyEnquiryController::class, 'store']);
  Route::post('/add-user-create-listing', [UserCreateListingController::class, 'store']);
  Route::get('/get-single-pages/{slug}', [PageController::class, 'showFrontend']);
  Route::get('/get-active-pages', [PageController::class, 'getActivePages']);
  Route::post('/update-views/{slug}', [PropertyController::class, 'updateViews']);
  Route::get('/get-all-property-types', [PropertyTypeController::class, 'index']);
  Route::get('/get-all-cities', [CityController::class, 'index']);
  Route::get('/get-all-counters', [CounterController::class, 'index']);
  Route::get('/fetch-property-through-city-and-type', [PropertyController::class, 'fetchByCityAndType']);
  Route::get('/get-all-featured-properties', [PropertyController::class, 'getFeaturedProperties']);
  Route::get('/get-all-testimonials', [TestimonialController::class, 'index']);
  Route::get('/get-single-properties-web/{slug}', [PropertyController::class, 'showWeb']);
  Route::get('/get-property-count-by-type', [PropertyTypeController::class, 'getAllPropertyTypesWithCount']);
  Route::get('/get-top-properties', [PropertyController::class, 'getTopProperties']);
  Route::get('/get-area-by-city-web/{id}', [AreaController::class, 'getAreasByCity']);
  Route::get('/get-all-blogs', [BlogController::class, 'index']);
  Route::get('/get-single-blog/{id}', [BlogController::class, 'singleBlog']);
  Route::get('/get-company-details', [CompanyDetailsController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected APIs with Passport token)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
  // Public route: login
  Route::post('/login', [AuthController::class, 'login']);

  // Protected routes
  Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User management
    Route::get('/get-all-users', [UserManagementController::class, 'index']);
    Route::post('/add-new-user', [UserManagementController::class, 'store']);
    Route::put('/update-users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/delete-users/{id}', [UserManagementController::class, 'destroy']);
    Route::get('/get-profile', [UserManagementController::class, 'profile']);
    Route::put('/change-password/{id}', [UserManagementController::class, 'changePassword']);

    // Amenity management
    Route::get('/get-all-amenities', [AmenityController::class, 'index']);
    Route::post('/add-amenities', [AmenityController::class, 'store']);
    Route::put('/edit-amenities/{id}', [AmenityController::class, 'update']);
    Route::delete('/delete-amenities/{id}', [AmenityController::class, 'destroy']);

    // NearbyLocations management
    Route::get('/get-all-nearby-locations', [NearbyLocationsController::class, 'index']);
    Route::post('/add-nearby-locations', [NearbyLocationsController::class, 'store']);
    Route::put('/edit-nearby-locations/{id}', [NearbyLocationsController::class, 'update']);
    Route::delete('/delete-nearby-locations/{id}', [NearbyLocationsController::class, 'destroy']);

    // Contact enquiry management
    Route::get('/get-all-contact-enquiries', [ContactEnquiryController::class, 'index']);
    Route::put('/edit-contact-enquiries/{id}', [ContactEnquiryController::class, 'updateStatus']);

    // Property type management
    Route::get('/get-all-property-types', [PropertyTypeController::class, 'index']);
    Route::post('/add-property-type', [PropertyTypeController::class, 'store']);
    Route::put('/edit-property-type/{id}', [PropertyTypeController::class, 'update']);
    Route::delete('/delete-property-type/{id}', [PropertyTypeController::class, 'destroy']);

    // Blog management
    Route::get('/get-all-blogs', [BlogController::class, 'index']);
    Route::post('/add-blog', [BlogController::class, 'store']);
    Route::put('/edit-blog/{id}', [BlogController::class, 'update']);
    Route::delete('/delete-blog/{id}', [BlogController::class, 'destroy']);
    Route::get('/get-single-blog/{id}', [BlogController::class, 'singleBlog']);

    // testimonials
    Route::get('/get-all-testimonials', [TestimonialController::class, 'index']);
    Route::post('/add-testimonial', [TestimonialController::class, 'store']);
    Route::put('/update-testimonial/{id}', [TestimonialController::class, 'update']);
    Route::delete('/delete-testimonial/{id}', [TestimonialController::class, 'destroy']);

    // states
    Route::get('/get-all-states', [StateController::class, 'index']);
    Route::post('/add-state', [StateController::class, 'store']);
    Route::put('/update-state/{id}', [StateController::class, 'update']);
    Route::delete('/delete-state/{id}', [StateController::class, 'destroy']);
    Route::get('/search-state', [StateController::class, 'searchState']);

    // cities
    Route::get('/get-all-cities', [CityController::class, 'index']);
    Route::post('/add-city', [CityController::class, 'store']);
    Route::post('/update-city/{id}', [CityController::class, 'update']);
    Route::delete('/delete-city/{id}', [CityController::class, 'destroy']);
    Route::get('/get-city-by-state/{id}', [CityController::class, 'getCitiesByState']);
    Route::get('/search-city', [CityController::class, 'searchCity']);
    
    // areas
    Route::get('/get-all-areas', [AreaController::class, 'index']);
    Route::post('/add-area', [AreaController::class, 'store']);
    Route::post('/update-area/{id}', [AreaController::class, 'update']);
    Route::delete('/delete-area/{id}', [AreaController::class, 'destroy']);
    Route::get('/get-area-by-city/{id}', [AreaController::class, 'getAreasByCity']);
    Route::get('/search-area', [AreaController::class, 'searchArea']);

    // counter
    Route::get('/get-all-counters', [CounterController::class, 'index']);
    Route::post('/add-counter', [CounterController::class, 'store']);
    Route::post('/update-counter/{id}', [CounterController::class, 'update']);
    Route::delete('/delete-counter/{id}', [CounterController::class, 'destroy']);

    // custom-pages
    Route::get('/get-all-pages', [PageController::class, 'index']); 
    Route::get('/get-single-page/{slug}', [PageController::class, 'showBySlug']);
    Route::post('/add-or-update-page', [PageController::class, 'storeOrUpdate']);

    // property-enquiries
    Route::get('/get-property-enquiries', [PropertyEnquiryController::class, 'index']);
    Route::put('/update-property-enquiries/{id}', [PropertyEnquiryController::class, 'updateStatus']);

    // user-create-listing
    Route::get('/get-user-create-listing', [UserCreateListingController::class, 'index']);
    Route::put('/update-user-create-listing/{id}', [UserCreateListingController::class, 'updateStatus']);

    // company-details
    Route::get('/get-company-details', [CompanyDetailsController::class, 'index']);
    Route::post('/update-company-details', [CompanyDetailsController::class, 'store']);
    Route::get('/get-dashboard-details', [CompanyDetailsController::class, 'getDashboard']);

    // properties management
    Route::get('get-all-properties', [PropertyController::class, 'index']);
    Route::get('get-single-properties/{slug}', [PropertyController::class, 'show']);
    Route::post('add-new-property', [PropertyController::class, 'store']);
    Route::post('update-property/{id}', [PropertyController::class, 'update']);
    Route::put('mark-as-favourite/{id}', [PropertyController::class, 'mark_as_favourite']);
    Route::delete('mark-as-deleted/{id}', [PropertyController::class, 'mark_as_deleted']);
  });
});
