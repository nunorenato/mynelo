<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boat_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id');
            $table->foreignId('user_id');
            $table->integer('seat_id')->nullable();
            $table->integer('seat_position')->nullable();
            $table->integer('seat_height')->nullable();
            $table->integer('footrest_id')->nullable();
            $table->integer('footrest_position')->nullable();
            $table->integer('rudder_id')->nullable();
            $table->string('paddle')->nullable();
            $table->string('paddle_length')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boat_registrations');
    }
};
