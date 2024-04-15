<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->foreignId('product_type_id')->after('external_id')->nullable()->constrained('product_types');
            $table->foreignId('discipline_id')->after('product_type_id')->nullable()->constrained('disciplines');
            $table->json('attributes')->after('discipline_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_type_id');
            $table->dropConstrainedForeignId('discipline_id');
            $table->dropColumn('attributes');
            $table->integer('category_id')->nullable();
        });
    }
};
