<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boat_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_id');
            $table->string('seller', 254)->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('boat_registrations', function (Blueprint $table) {
            $table->foreignId('seller_id')->after('user_id')->constrained(table:'dealers');
            $table->dropColumn('seller');
        });
    }
};
