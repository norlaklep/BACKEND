<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100)->nullable(false)->unique('places_title_unique');
            $table->string('description', 400)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('address_link', 255)->nullable();
            $table->string('image_placeholder', 255)->nullable();
            $table->string('image_gallery')->nullable();
            $table->uuid('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
};
