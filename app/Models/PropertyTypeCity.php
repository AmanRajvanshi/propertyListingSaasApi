<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTypeCity extends Model
{
  use HasFactory;

  protected $table = 'property_type_city';

  protected $fillable = [
    'property_id',
    'property_type_id',
    'city_id',
  ];

  public function property()
  {
    return $this->belongsTo(Properties::class);
  }

  public function propertyType()
  {
    return $this->belongsTo(PropertyType::class);
  }

  public function city()
  {
    return $this->belongsTo(City::class);
  }
}
