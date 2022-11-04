<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('games', function (Blueprint $table) {
      $table->id();
      $table->string('code', 6)->unique();
      $table->string('status')->default('created');
      $table->foreignId('created_by')->constrained('users');
      $table->json('players');
      $table->json('state')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('games');
  }

};
