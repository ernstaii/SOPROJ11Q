<?php

use App\Enums\Statuses;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default(Statuses::Config);
            $table->integer('duration')->default(120);
            $table->integer('interval')->default(60);
            $table->integer('time_left')->default(7200);
            $table->string('police_station_location', 255)->nullable();
            $table->integer('thieves_score')->default(0);
            $table->integer('police_score')->default(0);
            $table->timestamp('last_interval_at')->nullable();
            $table->timestamp('started_at')->nullable();
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
        Schema::dropIfExists('games');
    }
}
