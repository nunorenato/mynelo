<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'coach';
    public function up(): void
    {
        Schema::table('sierraw_train', function (Blueprint $table) {
            $table->dateTime('editedon')->nullable()->default(null)->change();
            $table->bigInteger('boatid')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sierraw_train', function (Blueprint $table) {
            $table->dateTime('editedon')->default('1970-01-01 00:00:00')->change();
            $table->bigInteger('boatid')->change();
        });
    }
};
