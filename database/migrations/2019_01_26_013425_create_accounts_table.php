<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('account_id')->comment('TwitterアカウントID');
            $table->string('username')->comment('myID URLに使用されている表示用ID')->nullable();
            $table->string('fullname')->comment('nickname 表示用のユーザー名')->nullable();

            $table->tinyInteger('login_method')->comment('ログイン方法')->nullable();
            $table->string('login_id')->nullable()->comment('ID');
            $table->string('password')->nullable()->comment('PW');
            $table->string('oauth_access_token')->nullable()->comment('ソーシャルログインで得たトークン（認証後に利用すること無いので要らないはず）');
            $table->string('oauth_access_secret')->nullable()->comment('ソーシャルログインで得たトークン（認証後に利用すること無いので要らないはず）');

            $table->timestamp('last_logged_in_at')->nullable();

            $table->tinyInteger('prefecture')->comment('都道府県ID')->nullable();

            $table->text('description')->comment('紹介文')->nullable();

            $table->string('web_url1')->comment('WEBサイト')->nullable();
            $table->string('web_url2')->comment('WEBサイト')->nullable();
            $table->string('web_url3')->comment('WEBサイト')->nullable();

            $table->text('img_thumnail_url')->comment('サムネイル画像のURL')->nullable();
            $table->text('img_cover_url')->comment('サムネイル画像のURL')->nullable();

            $table->integer('score')->comment('共通のスコア 簡易判断用')->nullable();
            $table->integer('total_post')->comment('ポスト数 掲載されていた数 簡易判断用')->nullable();
            $table->integer('total_follow')->comment('フォロー数 掲載されていた数 簡易判断用')->nullable();
            $table->integer('total_follower')->comment('フォロワー数 掲載されていた数 簡易判断用')->nullable();

            $table->datetime('last_posted_at')->comment('アクティブ率 最後に投稿した日時')->nullable();
            $table->integer('total_likes')->comment('アクティブ率 いいねしている数')->nullable();

            $table->datetime('reviewed_at')->comment('対象をクローリングして情報更新した日時')->nullable();

            $table->boolean('is_private')->comment('鍵アカウントか？')->nullable();
            $table->boolean('is_salon_account')->comment('サロンアカウントか？')->nullable();

            $table->boolean('hidden_from_auto_follow')->comment('自動フォローする対象から除外する')->nullable();
            $table->boolean('hidden_from_search')->comment('検索対象から除外する（このアプリからはサロンアカウントとして存在しない扱い）')->nullable();

            $table->timestamps();
        });

        Schema::table('accounts', function ($table) {
            // INDEX・外部キー
            $table->primary('account_id');
            $table->unique('username');

            $table->index('score');
            $table->index('total_post');
            $table->index('total_follow');
            $table->index('total_follower');

            $table->index('hidden_from_auto_follow');
            $table->index('hidden_from_search');

            $table->index('reviewed_at');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
