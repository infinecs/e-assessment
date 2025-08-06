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
        Schema::table('assessment', function (Blueprint $table) {
            $table->unsignedBigInteger('EventID')->nullable()->after('ParticipantID');
            $table->foreign('EventID')->references('EventID')->on('assessment_event')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment', function (Blueprint $table) {
            $table->dropForeign(['EventID']);
            $table->dropColumn('EventID');
        });
    }
};
