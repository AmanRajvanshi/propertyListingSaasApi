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
    Schema::table('pages', function (Blueprint $table) {
      $table->string('meta_title')->nullable();
      $table->text('meta_description')->nullable();
      $table->text('meta_keywords')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('pages', function (Blueprint $table) {
      $table->dropColumn('meta_title');
      $table->dropColumn('meta_description');
      $table->dropColumn('meta_keywords');
    });
  }
};
