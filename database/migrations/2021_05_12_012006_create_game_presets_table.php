<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamePresetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('duration');
            $table->string('interval');
            $table->string('police_station_location', 255)->nullable();
            $table->string('colour_theme')->default('#0099ff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_presets');
    }
}
