<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->float('co2')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->dropColumn('co2');
        });
    }
};
