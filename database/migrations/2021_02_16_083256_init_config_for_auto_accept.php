<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitConfigForAutoAccept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE configurations SET
            auto_follow_back = 0,
            auto_reject	= 0,
            follow_back_only_tweets_more_than = -1,
            follow_back_only_profile_contains = ''
        ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
