<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessmentevent', function (Blueprint $table) {
            $table->json('TopicWeightages')->nullable()->after('TopicID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessmentevent', function (Blueprint $table) {
            $table->dropColumn('TopicWeightages');
        });
    }
};
