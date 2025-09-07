<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
  protected $table = 'property_images';

  protected $fillable = [
    'property_id',
    'image_path',
    'alt_text',
    'is_main'
  ];

  public function property()
  {
    return $this->belongsTo(Properties::class, 'property_id');
  }
}
