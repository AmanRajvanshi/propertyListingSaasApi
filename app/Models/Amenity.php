<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
  protected $table = 'amenities';

  protected $fillable = [
    'name',
  ];

  public function properties()
  {
    return $this->belongsToMany(
      Properties::class,
      'property_amenities',
      'amenity_id',
      'property_id'
    );
  }
}
