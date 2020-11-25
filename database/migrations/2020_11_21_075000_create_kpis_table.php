<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->date('date');

            // アカウント作成前は不明なのでNULL
            $table->integer('accounts_total')->comment('アカウント数')->nullable();
            $table->integer('salon_accounts_total')->comment('確認サロンアカウント数')->nullable();
            $table->integer('salon_accounts_active')->comment('アクティブアカウント数')->nullable();
            $table->integer('registered_accounts_total')->comment('アプリ利用者数')->nullable();
            $table->integer('registered_accounts_active')->comment('アプリアクティブ利用者数')->nullable();
            $table->integer('rejected_accounts_total')->comment('アプリ利用拒否者数')->nullable();
            $table->integer('reviewed_accounts')->comment('プロフィール更新数')->nullable();
            $table->integer('created_accounts')->comment('新規登録数')->nullable();
            $table->integer('started_accounts_2w')->comment('過去2週間に利用開始したアカウント')->nullable();
            $table->integer('api_called_total')->comment('TwitterAPI コール数')->nullable();
            $table->datetime('oldest_review_datetime')->comment('最後にレビューしたアカウントの日時')->nullable();
            $table->timestamps();

        });

        Schema::table('kpis', function ($table) {
            $table->primary('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpis');
    }
}
