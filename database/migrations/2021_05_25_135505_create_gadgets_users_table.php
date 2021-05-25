<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGadgetsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gadgets_users', function (Blueprint $table) {
            $table->unsignedBigInteger('gadget_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('amount');

            $table->foreign('gadget_id')
                ->references('id')->on('gadgets');
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
        Schema::dropIfExists('gadgets_users');
    }
}
