<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 1)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('club')->nullable();
            $table->integer('weekly_trainings')->nullable();
            $table->foreignId('discipline_id')->nullable()->constrained();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
            $table->dropColumn('photo');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('gender');
            $table->dropColumn('club');
            $table->dropColumn('weekly_trainings');
            $table->dropConstrainedForeignId('discipline_id');
        });
    }
};
