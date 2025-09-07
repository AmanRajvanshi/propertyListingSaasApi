<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
  protected $fillable = [
    'company_name',
    'company_email',
    'company_phone1',
    'company_phone2',
    'company_address',
    'company_facebook',
    'company_twitter',
    'company_instagram',
    'company_linkedin',
    'company_youtube',
    'company_google',
  ];
}
