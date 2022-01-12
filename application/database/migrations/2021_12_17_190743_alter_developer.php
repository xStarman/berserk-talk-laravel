<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDeveloper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->dropColumn("hobby");
            $table->bigInteger("hobby_id")->unsigned();
        });
        Schema::table('developers', function (Blueprint $table) {
            $table->foreign('hobby_id')->references('id')->on('hobbies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->dropColumn("hobby_id");
            $table->string("hobby");
        });
    }
}
