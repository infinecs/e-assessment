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
        Schema::create('assessment_topic', function (Blueprint $table) {
            $table->bigIncrements('TopicID'); // Primary Key
            $table->string('TopicName');
            $table->unsignedBigInteger('QuestionID');
            $table->unsignedBigInteger('AdminID');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_topic');
    }
};
