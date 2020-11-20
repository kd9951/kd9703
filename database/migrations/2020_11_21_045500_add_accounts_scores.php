<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountsScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('location')->comment('発信地')->nullable()->after('prefecture');
            $table->integer('total_listed')->comment('リストされた数')->nullable()->after('total_follower');
            $table->datetime('started_at')->comment('アカウントの利用開始日時')->nullable()->after('last_posted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->dropColumn('total_listed');
            $table->dropColumn('started_at');
        });
    }
}
