<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boat_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id')->constrained('boats');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('attribute_id')->nullable()->constrained('attributes');
            //$table->primary(['boat_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boat_product');
    }
};
