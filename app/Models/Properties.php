<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
  protected $table = 'properties';

  protected $fillable = [
    'property_title',
    'property_description',
    'property_street_address',
    'state_id',
    'city_id',
    'area_id',
    'property_type',
    'property_rent',
    'property_rent_frequency',
    'sharing_type',
    'occupancy_type',
    'no_of_rooms',
    'no_of_bathrooms',
    'year_built',
    'map',
    'status',
    'is_property_favourite',
    'slug',
    'views',
    'meta_title',
    'meta_description',
    'meta_keywords',
  ];

  protected $casts = [
    'meta_keywords' => 'array',
    'is_property_favourite' => 'boolean',
  ];

  public function state()
  {
    return $this->belongsTo(State::class);
  }

  public function city()
  {
    return $this->belongsTo(City::class);
  }

  public function area()
  {
    return $this->belongsTo(Area::class);
  }

  public function propertyType()
  {
    return $this->belongsTo(PropertyType::class, 'property_type', 'id');
  }

  public function typeCityLinks()
  {
    return $this->hasMany(PropertyTypeCity::class, 'property_id', 'id');
  }

  public function amenities()
  {
    return $this->belongsToMany(
      Amenity::class,
      'property_amenities',
      'property_id',
      'amenity_id'
    );
  }

  public function nearbyLocations()
  {
    return $this->belongsToMany(
      NearbyLocation::class,
      'property_nearby_locations',
      'property_id',
      'nearby_location_id'
    );
  }

  public function images()
  {
    return $this->hasMany(PropertyImage::class, 'property_id');
  }

  public function multiplePricings()
  {
    return $this->hasMany(MultiplePricingsOfProperty::class, 'property_id');
  }
}
