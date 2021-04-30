<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorderMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('border_markers', function (Blueprint $table) {
            $table->id();
            $table->string('location', 255);
            $table->unsignedBigInteger('game_id');
            $table->foreign('game_id')
                ->references('id')->on('games');
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
        Schema::dropIfExists('border_markers');
    }
}
