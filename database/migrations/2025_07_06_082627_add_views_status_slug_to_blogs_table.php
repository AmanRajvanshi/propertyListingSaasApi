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
    Schema::table('blogs', function (Blueprint $table) {
      $table->integer('views')->default(0);
      $table->string('slug')->nullable();
      $table->enum('status', ['active', 'draft'])->default('active');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('blogs', function (Blueprint $table) {
      $table->dropColumn('views');
      $table->dropColumn('slug');
      $table->dropColumn('status');
    });
  }
};
