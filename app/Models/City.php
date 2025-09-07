<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  use HasFactory;

  protected $fillable = ['city_name', 'state_id', 'image', 'slug', 'status', 'is_main'];

  public function state()
  {
    return $this->belongsTo(State::class);
  }

  public function areas()
  {
    return $this->hasMany(Area::class);
  }
}
