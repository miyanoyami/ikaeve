<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weapons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('名前')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('user_id');
            $table->unsignedInteger('team_id')->comment('チームID');
            $table->string('name', 30)->comment('名前')->nullable();
            $table->unsignedInteger('xp')->comment('xp')->nullable();
            $table->unsignedInteger('weapon_id')->comment('使用武器')->nullable();
            $table->text('note')->comment('備考')->nullable();
            $table->boolean('ban')->comment('拒否')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('weapon_id')->references('id')->on('weapons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('no')->comment('no')->nullable();
            $table->unsignedInteger('event_id')->comment('大会ID')->nullable();
            $table->string('name', 50)->comment('名前')->nullable();
            $table->unsignedInteger('member_id')->comment('代表者ID')->nullable();
            $table->string('friend_code', 255)->comment('フレンドコード')->nullable();
            $table->text('note')->comment('備考')->nullable();
            $table->boolean('approval')->comment('許可')->default(0);
            $table->boolean('abstention')->comment('棄権')->default(0);
            $table->unsignedInteger('xp_total')->comment('合計XP')->nullable();
            $table->string('block', 1)->comment('A~P?ブロック')->nullable();
            $table->unsignedInteger('sheet')->comment('シート')->nullable();
            $table->unsignedInteger('number')->comment('1~4')->nullable();
            $table->boolean('change_flg')->comment('チェンジ対象')->default(0);
            $table->unsignedInteger('pre_rank')->comment('予選ランク')->nullable();
            $table->boolean('main_game')->comment('本戦進出')->default(0);
            $table->unsignedInteger('main_rank')->comment('本戦ランク')->nullable();
            $table->boolean('final_game')->comment('決勝戦進出')->default(0);
            $table->unsignedInteger('final_rank')->comment('最終')->nullable();
            $table->string('pre_rule1', 255)->comment('予選ルール1')->nullable();
            $table->string('pre_rule2', 255)->comment('予選ルール2')->nullable();
            $table->string('pre_rule3', 255)->comment('予選ルール3')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('wanteds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('user_id');
            $table->unsignedInteger('event_id')->comment('大会ID')->nullable();
            $table->string('xp', 255)->comment('xp')->nullable();
            $table->string('wepons', 255)->comment('持ち武器')->nullable();
            $table->text('note')->comment('内容')->nullable();
            $table->boolean('end')->comment('終了')->default(0);

            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('wanteds');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('members');
        Schema::dropIfExists('weapons');
    }
}
