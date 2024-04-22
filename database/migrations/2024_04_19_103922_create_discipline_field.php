<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discipline_field', function (Blueprint $table) {
            $table->foreignId('discipline_id')->constrained('disciplines');
            $table->foreignId('field_id')->constrained('fields');
            $table->boolean('required');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_field');
    }
};
