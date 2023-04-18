<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->comment('イベントID')->nullable();
            $table->unsignedInteger('win_team_id')->comment('勝利チームID')->nullable();
            $table->unsignedInteger('lose_team_id')->comment('敗北チームID')->nullable();
            $table->tinyInteger('win_score')->comment('勝利点')->nullable();
            $table->tinyInteger('lose_score')->comment('敗北点')->nullable();
            $table->boolean('unearned_win')->comment('不戦勝(0:なし/1:あり)')->default(0);
            $table->string('block', 1)->comment('A~P?ブロック')->nullable();
            $table->unsignedInteger('sheet')->comment('シート')->nullable();
            $table->unsignedInteger('turn')->comment('1~4')->nullable();
            $table->unsignedInteger('user_id')->comment('更新者')->nullable();
            $table->text('memo')->comment('メモ')->nullable();
            $table->boolean('abstention')->comment('両棄権(1:棄権)')->default(0);
            $table->unsignedInteger('level')->comment('0:予選1:本戦2:決勝')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('win_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('lose_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
