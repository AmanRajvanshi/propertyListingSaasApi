<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetails;
use App\Models\ContactEnquiry;
use App\Models\Properties;
use App\Models\PropertyEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyDetailsController extends Controller
{
  // get company details
  public function index(): JsonResponse
  {
    $company = CompanyDetails::first();
    return response()->json([
      'status' => true,
      'message' => 'Company details fetched successfully.',
      'data' => $company,
    ]);
  }

  //  insert and update company details
  public function store(Request $request): JsonResponse
  {

    $request->validate(
      [
        'company_name' => 'required|string',
        'company_address' => 'required|string',
        'company_email' => 'required|email',
        'company_phone1' => 'required|string',
      ]
    );

    $company = CompanyDetails::updateOrCreate(
      ['id' => $request->id],
      [
        'company_name' => $request->company_name,
        'company_address' => $request->company_address,
        'company_email' => $request->company_email,
        'company_phone1' => $request->company_phone1,
        'company_phone2' => $request->company_phone2,
        'company_facebook' => $request->company_facebook,
        'company_twitter' => $request->company_twitter,
        'company_instagram' => $request->company_instagram,
        'company_linkedin' => $request->company_linkedin,
        'company_youtube' => $request->company_youtube,
        'company_google' => $request->company_google,
      ]
    );

    return response()->json([
      'status' => true,
      'message' => 'Company details updated successfully.',
      'data' => $company,
    ]);
  }

  public function getDashboard(): JsonResponse
{
    $properties = Properties::count();
    $enquiries = ContactEnquiry::count();
    $views = Properties::sum('views');
    $contactEnquiries = PropertyEnquiry::count();

    $months = collect(range(0, 5))->map(function ($i) {
        return Carbon::now()->subMonths(5 - $i)->format('M');
    })->toArray();

    $startDate = Carbon::now()->subMonths(5)->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();

    $data = Properties::selectRaw("
        property_type,
        DATE_FORMAT(created_at, '%b') as month,
        COUNT(*) as count
    ")
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('property_type', DB::raw("DATE_FORMAT(created_at, '%b')"))
        ->get();

    $types = \App\Models\PropertyType::pluck('name', 'id');

    $series = [];
    foreach ($types as $typeId => $typeName) {
        $counts = [];
        foreach ($months as $month) {
            $entry = $data->where('property_type', $typeId)
                          ->firstWhere('month', $month);
            $counts[] = $entry ? (int)$entry->count : 0;
        }
        $series[] = [
            'name' => $typeName,
            'data' => $counts,
        ];
    }

    return response()->json([
      'status' => true,
      'message' => 'Dashboard fetched successfully.',
      'data' => [
        'properties' => $properties,
        'enquiries' => $enquiries,
        'views' => $views,
        'contactEnquiries' => $contactEnquiries,
        'propertyGraph' => [
            'categories' => $months,
            'series' => $series,
        ],
      ],
    ]);
}

}
