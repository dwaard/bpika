<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreDataToStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->renameColumn('name', 'code');
        });
        Schema::table('stations', function (Blueprint $table) {
            $table->string('code')->primary()->change();
            $table->string('city');
            $table->string('name');
            $table->string('chart_color');
            // Latitude values range from S -90 to N +90
            $table->float('latitude', 22, 20);
            // Latitude values range from W -180 to E +180
            $table->float('longitude', 23, 20);
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
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropPrimary('code');
        });
        Schema::table('stations', function (Blueprint $table) {
            $table->renameColumn('code', 'name');
            $table->dropColumn('city');
            $table->dropColumn('chart_color');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('timezone');
            $table->dropColumn('enabled');

        });
    }
}
