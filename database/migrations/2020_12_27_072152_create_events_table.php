<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('名前')->nullable();
            $table->unsignedInteger('user_id')->comment('主催者ID')->nullable();
            $table->unsignedInteger('kind')->comment('種別')->nullable();
            $table->text('note')->comment('概要')->nullable();
            $table->datetime('from_date')->comment('開催開始日時')->nullable();
            $table->datetime('to_date')->comment('開催終了日時')->nullable();
            $table->datetime('from_recruit_date')->comment('募集開始日時')->nullable();
            $table->datetime('to_recruit_date')->comment('募集終了日時')->nullable();
            $table->unsignedInteger('min_team')->comment('最小チーム数')->nullable();
            $table->unsignedInteger('max_team')->comment('最大チーム数')->nullable();
            $table->unsignedInteger('team_member')->comment('チーム人数')->nullable();
            $table->string('delivery_url', 255)->comment('配信URL')->nullable();
            $table->string('delivery_name', 255)->comment('配信者')->nullable();
            $table->string('camera_url', 255)->comment('カメラURL')->nullable();
            $table->string('camera_name', 255)->comment('カメラマン')->nullable();
            $table->string('gameplay_url1', 255)->comment('実況URL1')->nullable();
            $table->string('gameplay_name1', 255)->comment('実況者1')->nullable();
            $table->string('gameplay_url2', 255)->comment('実況URL2')->nullable();
            $table->string('gameplay_name2', 255)->comment('実況者2')->nullable();
            $table->string('gameplay_url3', 255)->comment('実況URL3')->nullable();
            $table->string('gameplay_name3', 255)->comment('実況者3')->nullable();
            $table->string('gameplay_url4', 255)->comment('実況URL4')->nullable();
            $table->string('gameplay_name4', 255)->comment('実況者4')->nullable();
            $table->unsignedInteger('passing_order')->comment('予選通過順位')->default(0);
            $table->unsignedInteger('pre_score')->comment('予選先取点')->default(0);
            $table->unsignedInteger('main_score')->comment('本戦先取点')->default(0);
            $table->unsignedInteger('final_score')->comment('決勝先取点')->default(0);
            $table->boolean('view')->comment('表示(0:表示/1:非表示)')->default(0);
            $table->unsignedInteger('grade')->comment('大会グレード')->nullable();
            $table->boolean('point')->comment('ポイント追加するか否か(0:非対象/1:対象)')->default(0);
            $table->boolean('shuffle')->comment('本戦2回戦のシャッフル(0:非対象/1:対象)')->default(0);

            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('events');
    }
}
