<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameLootTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_loot', function (Blueprint $table) {
            $table->unsignedBigInteger('game_id')->index();
            $table->foreign('game_id')->references('id')->on('games');
            $table->unsignedBigInteger('loot_id')->index();
            $table->foreign('loot_id')->references('id')->on('loot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_loot');
    }
}
