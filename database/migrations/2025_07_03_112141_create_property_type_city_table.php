<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('property_type_city', function (Blueprint $table) {
      $table->id();
      $table->foreignId('property_id')->constrained()->onDelete('cascade');
      $table->foreignId('property_type_id')->constrained('property_types')->onDelete('cascade');
      $table->foreignId('city_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('property_type_city');
  }
};
