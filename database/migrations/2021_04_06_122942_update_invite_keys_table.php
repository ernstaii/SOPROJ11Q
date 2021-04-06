<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInviteKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invite_keys', function (Blueprint $table) {
            $table->string('role', 20)->default(Roles::None);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invite_keys', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
