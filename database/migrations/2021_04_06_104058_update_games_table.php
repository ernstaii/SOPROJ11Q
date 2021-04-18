<?php

use App\Enums\Statuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('status', 20)->default(Statuses::Config);
            $table->integer('duration')->default(120);
            $table->integer('interval')->default(60);
            $table->integer('time_left')->default(7200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('duration');
            $table->dropColumn('interval');
            $table->dropColumn('time_left');
        });
    }
}
