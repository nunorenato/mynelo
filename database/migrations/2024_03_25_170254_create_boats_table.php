<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boats', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->date('finished_at')->nullable();
            $table->float('finished_weight')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->float('ideal_weight')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};
