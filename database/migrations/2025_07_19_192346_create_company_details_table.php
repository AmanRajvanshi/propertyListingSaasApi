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
    Schema::create('company_details', function (Blueprint $table) {
      $table->id();
      $table->string('company_name');
      $table->string('company_email');
      $table->string('company_phone1');
      $table->string('company_phone2')->nullable();
      $table->string('company_address');
      $table->string('company_facebook')->nullable();
      $table->string('company_twitter')->nullable();
      $table->string('company_instagram')->nullable();
      $table->string('company_linkedin')->nullable();
      $table->string('company_youtube')->nullable();
      $table->string('company_google')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('company_details');
  }
};
