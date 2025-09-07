<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('property_amenities', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('property_id');
      $table->unsignedBigInteger('amenity_id');
      $table->timestamps();

      $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
      $table->foreign('amenity_id')->references('id')->on('amenities')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('property_amenities');
  }
};
