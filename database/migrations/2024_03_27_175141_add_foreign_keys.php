<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('country_id', 'user_country_foreign')->references('id')->on('countries');
        });
        Schema::table('boats', function (Blueprint $table) {
            $table->foreign('product_id', 'boat_product_foreign')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('user_country_foreign');
        });
        Schema::table('boats', function (Blueprint $table) {
            $table->dropForeign('boat_product_foreign');
        });
    }
};
