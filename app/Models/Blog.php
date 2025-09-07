<?php

// app/Models/Blog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
  protected $fillable = [
    'title',
    'description',
    'image',
    'meta_title',
    'meta_keywords',
    'meta_description',
    'slug',
    'views',
    'status'
  ];
}
