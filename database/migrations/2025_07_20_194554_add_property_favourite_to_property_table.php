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
    Schema::table('properties', function (Blueprint $table) {
      $table->boolean('is_property_favourite')->default(false)->after('status');
      $table->string('slug')->after('is_property_favourite');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('properties', function (Blueprint $table) {
      $table->dropColumn('is_property_favourite');
      $table->dropColumn('slug');
    });
  }
};
