<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // follower_idのユーザがfollowee_idのユーザをフォローしている
        Schema::create('follows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('follower_id'); // フォローしているユーザID
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('followee_id'); // フォローされているユーザID
            $table->foreign('followee_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follows');
    }
}
