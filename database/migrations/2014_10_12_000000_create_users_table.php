<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 191)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('twitter_id', 50)->comment('twitterId')->unique()->nullable();
            $table->string('twitter_nickname', 50)->comment('twitterニックネーム')->nullable();
            $table->unsignedInteger('role')->comment('権限(1:admin/2:staff/3:member)')->default(3);
            $table->boolean('twitter_auth')->comment('認証(0:未認証,1:認証済)')->default(0);
            $table->unsignedInteger('point')->comment('ポイント')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
