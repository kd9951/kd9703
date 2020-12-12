<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->string('post_id')->comment('内部ID');
            $table->boolean('is_private')->comment('私信フラグ Twitterでいうダイレクトメッセージ');
            $table->string('account_id')->comment('発信者');
            $table->string('in_reply_to_account_id')->nullable()->comment('この投稿が返信である時の返信先のアカウント');
            $table->string('in_reply_to_post_id')->nullable()->comment('この投稿が返信である時の返信先の投稿');

            $table->string('url')->comment('媒体の投稿ページURL');

            $table->text('title')->comment('タイトル(無い媒体が多い)');
            $table->text('body')->comment('本文');

            $table->text('img_thumnail_url')->nullable()->comment('サムネイル画像のURL');
            $table->text('img_main_url')->nullable()->comment('メイン画像のURL');

            $table->integer('score')->comment('媒体共通の評価値')->default(0);
            $table->integer('count_liked')->comment('いいねされている数 掲載されていた数 簡易判断用')->nullable();
            $table->integer('count_comment')->comment('コメント数 掲載されていた数 簡易判断用')->nullable();
            $table->integer('count_shared')->comment('シェア数・リツイート数 簡易判断用')->nullable();

            $table->datetime('posted_at')->comment('投稿日時')->nullable();
            $table->datetime('reviewed_at')->comment('対象をクローリングして情報更新した日時')->nullable();

            $table->timestamps();
        });

        Schema::table('posts', function ($table) {
            // INDEX・外部キー
            $table->primary('post_id');
            $table->index(['account_id', 'posted_at'], 'IDX_LATEST');
            $table->index('in_reply_to_account_id');
            $table->index('in_reply_to_post_id');
            // $table->foreign('account_id')->references('account_id')->on('owners')->onDelete('cascade');

            $table->index('score');

            $table->index('reviewed_at');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::create('post_recipients', function (Blueprint $table) {
            $table->string('post_id')->comment('対象投稿の内部ID');
            $table->string('from_account_id')->comment('送信者');
            $table->string('to_account_id')->comment('受信者');
            $table->datetime('posted_at')->comment('投稿日時')->nullable();
        });

        Schema::table('post_recipients', function ($table) {
            // INDEX・外部キー
            $table->index('post_id');
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('posted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_recipients');
        Schema::dropIfExists('posts');
    }
}
