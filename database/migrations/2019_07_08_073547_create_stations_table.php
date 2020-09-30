<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->string('code')->primary();
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
        Schema::dropIfExists('stations');
    }
}
