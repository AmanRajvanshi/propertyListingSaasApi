<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyEnquiry extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'email',
    'phone',
    'message',
    'property_id',
    'status'
  ];

  public function property()
  {
    return $this->belongsTo(Properties::class);
  }
}
