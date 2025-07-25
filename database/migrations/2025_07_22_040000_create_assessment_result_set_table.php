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
        Schema::create('assessment_result_set', function (Blueprint $table) {
            $table->bigIncrements('AssessmentID'); // Primary Key
            $table->unsignedBigInteger('QuestionID'); // Foreign Key
            $table->string('Answer');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_result_set');
    }
};
