<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NearbyLocation extends Model
{
  protected $table = 'nearby_locations';

  protected $fillable = [
    'nearby_location_name',
  ];

  public function properties()
  {
    return $this->belongsToMany(
      Properties::class,
      'property_nearby_locations',
      'nearby_location_id',
      'property_id'
    );
  }
}
