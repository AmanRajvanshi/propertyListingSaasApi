<?php

// database/migrations/xxxx_xx_xx_create_contact_enquiries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactEnquiriesTable extends Migration
{
  public function up()
  {
    Schema::create('contact_enquiries', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email');
      $table->string('phone');
      $table->string('subject');
      $table->text('message');
      $table->enum('status', ['pending', 'responded', 'resolved', 'reopened', 'closed'])->default('pending');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('contact_enquiries');
  }
}
