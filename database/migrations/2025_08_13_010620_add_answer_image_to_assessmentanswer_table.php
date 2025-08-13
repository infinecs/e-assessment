<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('assessmentanswer', function (Blueprint $table) {
        $table->string('AnswerImage')->nullable()->after('AnswerText'); 
    });
}

public function down(): void
{
    Schema::table('assessmentanswer', function (Blueprint $table) {
        $table->dropColumn('AnswerImage');
    });
}

};
