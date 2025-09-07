<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCreateListing extends Model
{
  protected $fillable = [
    'name',
    'email',
    'phone',
    'message',
    'property_type',
    'status',
  ];
}
