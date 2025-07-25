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
        Schema::create('assessment_participant', function (Blueprint $table) {
            $table->bigIncrements('ParticipantID'); // Primary Key
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('Nationality');
            $table->string('ContactNo');
            $table->string('Email');
            $table->string('JobType');
            $table->unsignedBigInteger('EventID');
            $table->string('UniversityInstitute');
            $table->string('FieldOfStud');
            $table->string('Qualification');
            $table->string('QualificationGrade');
            $table->integer('YearsOfExperience');
            $table->string('PositionApplied');
            $table->string('CurrentPosition');
            $table->string('CurrentCompany');
            $table->unsignedBigInteger('AdminID');
            $table->date('DateAvailable');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_participant');
    }
};
