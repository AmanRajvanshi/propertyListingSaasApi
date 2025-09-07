<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('properties', function (Blueprint $table) {
      $table->id();
      $table->string('property_title');
      $table->longText('property_description');
      $table->string('property_street_address')->nullable();

      // Foreign key columns
      $table->unsignedBigInteger('state_id');
      $table->unsignedBigInteger('city_id');
      $table->unsignedBigInteger('area_id');
      $table->unsignedBigInteger('property_type');

      $table->decimal('property_rent', 20, 2);
      $table->string('property_rent_frequency');
      $table->string('sharing_type');
      $table->string('occupancy_type');
      $table->integer('no_of_rooms')->nullable();
      $table->integer('no_of_bathrooms')->nullable();
      $table->year('year_built')->nullable();
      $table->enum('status', ['active', 'inactive', 'draft', 'deleted'])->default('active');
      $table->timestamps();

      // Foreign key constraints
      $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
      $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
      $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
      $table->foreign('property_type')->references('id')->on('property_types')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('properties');
  }
};
