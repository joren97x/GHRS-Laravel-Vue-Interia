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
        Schema::create('guest_houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id');
            $table->string('title');
            $table->string('description');
            $table->string('price');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('guests');
            $table->integer('rooms');
            $table->integer('bathrooms');
            $table->integer('beds');
            $table->json('amenities');
            $table->text('images');
            $table->string('status');
            $table->string('type');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_houses');
    }
};
