<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location', 255)->nullable()->change();
            $table->string('invite_key')->nullable();
            $table->string('role', 20)->default(Roles::None);

            $table->foreign('invite_key')->references('value')->on('invite_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location', 255)->change();
            $table->dropForeign('invite_key');
            $table->dropColumn('invite_key');
            $table->dropColumn('role');
        });
    }
}
