<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::table('products', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('image_id');
        });
        Schema::table('contents', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('image_id');
        });

        Schema::dropIfExists('boat_image');
        Schema::dropIfExists('images');

    }

};
