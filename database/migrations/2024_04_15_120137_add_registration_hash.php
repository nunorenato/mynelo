<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boat_registrations', function (Blueprint $table) {
            $table->string('hash', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('boat_registrations', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
};
