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
    Schema::create('property_enquiries', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email');
      $table->string('phone');
      $table->text('message')->nullable();
      $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
      $table->enum('status', ['pending', 'responded', 'resolved', 'reopened', 'closed'])->default('pending');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('property_enquiries');
  }
};
