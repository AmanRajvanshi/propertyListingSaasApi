<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('areas', function (Blueprint $table) {
      $table->id();
      $table->string('area_name');
      $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
      $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('areas');
  }
};
