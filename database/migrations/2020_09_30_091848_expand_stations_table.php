<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('code', 10)->default('');
        });
        // Copy content of name into code
        DB::table('stations')->update([
            'code' => DB::raw('name')
        ]);
        // Make code the PK and do the rest
        Schema::table('stations', function (Blueprint $table) {
            $table->string('code')->primary()->change();
            $table->string('city')->default('');
            $table->string('chart_color')->default('');
            // Latitude values range from S -90 to N +90
            $table->float('latitude', 22, 20)->default(0);
            // Latitude values range from W -180 to E +180
            $table->float('longitude', 23, 20)->default(0);
            $table->string('timezone')->default('Europe/Amsterdam');
            $table->boolean('enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Copy content of code into name
        DB::table('stations')->update([
            'name' => DB::raw('code')
        ]);
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('city');
            $table->dropColumn('chart_color');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('timezone');
            $table->dropColumn('enabled');
        });
    }
}
