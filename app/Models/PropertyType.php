<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
  protected $fillable = ['name', 'slug'];

  // In PropertyType model
  public function properties()
  {
    return $this->hasMany(Properties::class, 'property_type', 'id');
  }
}

