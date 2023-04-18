<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamEditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_edits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->comment('大会ID')->nullable();
            $table->string('name', 50)->comment('名前')->nullable();
            $table->string('block', 1)->comment('A~P?ブロック')->nullable();
            $table->unsignedInteger('sheet')->comment('シート')->nullable();
            $table->unsignedInteger('number')->comment('1~4')->nullable();
            $table->boolean('change_flg')->comment('チェンジ対象')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_edits');
    }
}
