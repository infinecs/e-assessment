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
        Schema::table('assessmentevent', function (Blueprint $table) {
            $table->string('EventPassword')->nullable()->after('EventName');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessmentevent', function (Blueprint $table) {
            $table->dropColumn('EventPassword');
        });
    }
};
