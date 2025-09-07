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
    Schema::table('cities', function (Blueprint $table) {
      $table->string('slug')->nullable()->after('image');
      $table->string('is_main')->nullable()->after('slug')->default('0');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('cities', function (Blueprint $table) {
      $table->dropColumn('slug');
      $table->dropColumn('is_main');
    });
  }
};
