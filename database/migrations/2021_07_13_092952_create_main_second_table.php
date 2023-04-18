<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_seconds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->comment('大会ID')->nullable();
            $table->unsignedInteger('team_id')->comment('チームID');
            $table->string('block', 1)->comment('A~P?ブロック')->nullable();
            $table->unsignedInteger('num')->comment('num')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_seconds');
    }
}
