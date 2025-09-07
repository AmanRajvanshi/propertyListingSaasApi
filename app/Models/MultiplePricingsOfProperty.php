<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiplePricingsOfProperty extends Model
{
  protected $table = 'multiple_pricings_of_property';

  protected $fillable = [
    'property_id',
    'property_rent',
    'property_rent_frequency',
    'sharing_type',
    'occupancy_type'
  ];
}
