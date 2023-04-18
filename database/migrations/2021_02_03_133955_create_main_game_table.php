<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('block', 1)->comment('A~P?ブロック')->nullable();
            $table->unsignedInteger('turn')->comment('何試合目')->nullable();
            $table->unsignedInteger('win_team_id')->comment('勝利チームID')->nullable();
            $table->unsignedInteger('lose_team_id')->comment('敗北チームID')->nullable();
            $table->tinyInteger('win_score')->comment('勝利点')->nullable();
            $table->tinyInteger('lose_score')->comment('敗北点')->nullable();
            $table->unsignedInteger('user_id')->comment('更新者')->nullable();
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
        Schema::dropIfExists('main_games');
    }
}
