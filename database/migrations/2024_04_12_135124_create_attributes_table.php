<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('product_type_id')->nullable()->constrained('product_types');
            $table->integer('external_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};