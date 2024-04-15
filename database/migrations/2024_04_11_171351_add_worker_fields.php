<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->foreignId('painter_id')->nullable()->constrained('people');
            $table->foreignId('layuper_id')->nullable()->constrained('people');
            $table->foreignId('evaluator_id')->nullable()->constrained('people');
        });
    }

    public function down(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('painter_id');
            $table->dropConstrainedForeignId('layuper_id');
            $table->dropConstrainedForeignId('evaluator_id');
        });
    }
};
