<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('multiple_pricings_of_property', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('property_id');
      $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
      $table->decimal('property_rent', 20, 2);
      $table->string('property_rent_frequency');
      $table->string('sharing_type');
      $table->string('occupancy_type');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('multiple_pricings_of_property');
  }
};
