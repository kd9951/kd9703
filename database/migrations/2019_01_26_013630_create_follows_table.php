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
        Schema::create('follows', function (Blueprint $table) {
            $table->increments('follow_id');

            $table->string('from_account_id')->comment('所有者');
            $table->string('to_account_id')->comment('フォローした人');

            $table->datetime('followed_at')->comment('フォローした日時');
            $table->tinyInteger('follow_method')->comment('FROMがTOをフォローしたトリガーは？')->nullable();

            $table->boolean('followed_back')->comment('フォローバックされたか？（反対のFollowと同期）')->default(false);
            $table->datetime('followed_back_at')->comment('フォローされた日時（反対のFollowと同期）')->nullable();
            $table->tinyInteger('followed_back_type')->comment('TOにとってFROMがフォローバックしてきたのは何がキッカケか？')->nullable();

            $table->boolean('unfollowed')->comment('フォロー解除されたか（レコード削除と同等だが統計に残る）')->default(false);
            $table->datetime('unfollowed_at')->comment('フォローされた日時（レコード削除と同等だが統計に残る）')->nullable();
            $table->tinyInteger('unfollowed_method')->comment('FROMがTOをフォロー解除したトリガーは？')->nullable();

            $table->timestamps();
        });

        Schema::table('follows', function ($table) {
            // INDEX・外部キー
            // $table->foreign('from_account_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('to_account_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['unfollowed', 'from_account_id', 'to_account_id'], 'EXISTS');

            // 現時点でのトータルカウント用
            $table->index(['to_account_id', 'unfollowed'], 'FOLLOWER_COUNT');
            $table->index(['from_account_id', 'unfollowed'], 'FOLLOW_COUNT');

            // 毎日の統計用（解除されていてもフォローした数に影響がない）
            $table->index(['to_account_id', 'followed_at', 'followed_back_type'], 'FOLLOWER_DAILY_COUNT');
            $table->index(['from_account_id', 'followed_at', 'follow_method'], 'FOLLOW_DAILY_COUNT');
            $table->index(['from_account_id', 'unfollowed_at', 'unfollowed_method'], 'UNFOLLOW_DAILY_COUNT');
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
