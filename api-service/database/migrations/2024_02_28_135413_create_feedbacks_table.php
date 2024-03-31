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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewee_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('appointment_id');
            $table->integer('rating'); // 1 - 5
            $table->text('comment');
            $table->timestamps();

            $table
                ->foreign('reviewee_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table
                ->foreign('reviewer_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table
                ->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
