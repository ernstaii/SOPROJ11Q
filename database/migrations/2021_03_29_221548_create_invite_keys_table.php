<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInviteKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_keys', function (Blueprint $table) {
            $table->string('value');
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('role', 20)->default(Roles::None);
            $table->timestamps();

            $table->primary('value');
            $table->foreign('game_id')
                ->references('id')->on('games');
            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keys');
    }
}
