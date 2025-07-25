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
        Schema::create('assessment_event', function (Blueprint $table) {
            $table->bigIncrements('EventID'); // Primary Key
            $table->string('EventName');
            $table->unsignedBigInteger('CategoryID');
            $table->string('EventCode');
            $table->integer('QuestionLimit');
            $table->integer('DurationEachQuestion');
            $table->unsignedBigInteger('TopicID');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_event');
    }
};
