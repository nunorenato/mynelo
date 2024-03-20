<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('competition')->after('gender')->default(1);
            $table->decimal('time_500', 6, 3, true)->nullable();
            $table->decimal('time_1000', 6, 3, true)->nullable();
            $table->boolean('alert_fill')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('competition');
            $table->dropColumn('time_500');
            $table->dropColumn('time_1000');
            $table->dropColumn('alert_fill');
        });
    }
};
