<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_answer', function (Blueprint $table) {
            $table->bigIncrements('AnswerID'); // Primary Key
            $table->unsignedBigInteger('QuestionID');
            $table->string('Answer');
            $table->text('AnswerText');
            $table->string('ExpectedAnswer');
            $table->unsignedBigInteger('AdminID');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_answer');
    }
};
