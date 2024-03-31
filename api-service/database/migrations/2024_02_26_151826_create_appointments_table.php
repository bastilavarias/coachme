<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('availability_id');
            $table->unsignedBigInteger('service_id');
            $table->date('date');
            $table->text('meeting_url');
            $table->string('status'); // Will create enums for this
            $table->timestamps();

            $table
                ->foreign('student_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table
                ->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table
                ->foreign('availability_id')
                ->references('id')
                ->on('availabilities')
                ->cascadeOnDelete();
            $table
                ->foreign('service_id')
                ->references('id')
                ->on('services')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
