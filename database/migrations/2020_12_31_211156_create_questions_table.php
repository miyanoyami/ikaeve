<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->comment('大会ID')->nullable();
            $table->string('title', 255)->comment('項目')->nullable();
            $table->boolean('required')->comment('必須')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question_id')->comment('question_id')->nullable();
            $table->unsignedInteger('team_id')->comment('チームID')->nullable();
            $table->string('note', 255)->comment('回答')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
    }
}
