<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->increments('notice_id');
            $table->string('account_id')->comment('通知先の対象者');

            $table->datetime('notified_at')->comment('通知日時');
            $table->tinyInteger('notice_type')->comment('通知タイプ');

            $table->text('title')->comment('タイトル');
            $table->text('body')->comment('本文');
            $table->string('from_account_id')->nullable()->comment('通知のアクションを起こした人');
            $table->string('post_id')->nullable()->comment('対象の投稿のID');

            $table->timestamps();
        });

        Schema::table('notices', function ($table) {
            // INDEX・外部キー
            // $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('from_account_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['account_id', 'notified_at'], 'USER_NOTIFIED_DATE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notices');
    }
}
