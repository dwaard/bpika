<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('station_name');
            $table->float('th_temp')->nullable();
            $table->float('th_hum')->nullable();
            $table->float('th_dew')->nullable();
            $table->float('th_heatindex')->nullable();
            $table->float('thb_temp')->nullable();
            $table->float('thb_hum')->nullable();
            $table->float('thb_dew')->nullable();
            $table->float('thb_press')->nullable();
            $table->float('thb_seapress')->nullable();
            $table->float('wind_wind')->nullable();
            $table->float('wind_avgwind')->nullable();
            $table->float('wind_dir')->nullable();
            $table->float('wind_chill')->nullable();
            $table->float('rain_rate')->nullable();
            $table->float('rain_total')->nullable();
            $table->float('uv_index')->nullable();
            $table->float('sol_rad')->nullable();
            $table->float('sol_evo')->nullable();
            $table->float('sun_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measurements');
    }
}
