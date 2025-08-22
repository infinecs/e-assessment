<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('assessmentevent')
            ->where('DateCreate', '0000-00-00 00:00:00')
            ->orWhereNull('DateCreate')
            ->update(['DateCreate' => now()]);

        DB::table('assessmentevent')
            ->where('DateUpdate', '0000-00-00 00:00:00')
            ->orWhereNull('DateUpdate')
            ->update(['DateUpdate' => now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is a data fix, so the down method is intentionally left empty.
        // Reverting this would require knowing the original invalid values, which is not practical.
    }
};
