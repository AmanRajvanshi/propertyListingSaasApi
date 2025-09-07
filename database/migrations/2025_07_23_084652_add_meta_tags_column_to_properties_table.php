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
      $table->text('meta_title')->nullable()->after('views');
      $table->text('meta_description')->nullable()->after('meta_title');
      $table->text('meta_keywords')->nullable()->after('meta_description');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('properties', function (Blueprint $table) {
      $table->dropColumn('meta_title');
      $table->dropColumn('meta_description');
      $table->dropColumn('meta_keywords');
    });
  }
};
