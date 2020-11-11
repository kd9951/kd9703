<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->string('account_id')->comment('アカウントID');

            $table->integer('auto_follow')->comment('条件に従い自動フォローする');
            $table->integer('target_follow_per_day')->comment('1日のフォロー目標数　最大500');
            $table->integer('sleep_hour_start')->comment('夜間はおやすみする　23時～6時');
            $table->integer('sleep_hour_end')->comment('夜間はおやすみする　23時～6時');
            $table->integer('auto_follow_back_before_accept')->comment('┗まだフォロー承認していないアカウントもフォロバする');
            $table->boolean('auto_follow_in_my_list')->comment('保存したリストのメンバーを自動的にフォローする');
            $table->boolean('auto_follow_target_list')->comment('┗リストを保存した他のアカウントも自動的にフォローする');
            $table->boolean('auto_follow_members')->comment('サロン垢村名簿のアカウントをフォロー');

            $table->boolean('follow_only_official_regulation')->comment('公式ルールに準拠したアカウントのみフォロー');
            $table->boolean('follow_only_set_icon')->comment('アイコンを設定しているアカウントのみフォロー');
            $table->integer('follow_only_tweets_more_than')->comment('最低ツイート数');
            $table->string('follow_only_profile_contains')->comment('プロフにこれらの言葉のどれかを含むアカウントのみフォロー（サロン垢ルール）');
            $table->string('follow_only_keyword_contains_1')->comment('このキーワードセットに該当するアカウントのみフォローする');
            $table->string('follow_only_keyword_contains_2')->comment('このキーワードセットに該当するアカウントのみフォローする');
            $table->string('follow_only_keyword_contains_3')->comment('このキーワードセットに該当するアカウントのみフォローする');

            $table->integer('follow_again_in_days')->comment('フォローしなかったアカウントを再確認するまでの日数（ゼロは二度としない）');

            $table->integer('auto_follow_back')->comment('自分をフォローしたアカウントを自動的にフォロバする');
            $table->integer('auto_reject')->comment('自分をフォローしたアカウントでもルール外なら削除する');
            $table->integer('check_follower_regulation')->comment('フォロワーがサロン垢ルールに従っているかチェックする');
            $table->integer('check_following_regulation')->comment('フォローしているアカウントがサロン垢ルールに従っているかチェックする');
            $table->boolean('follow_back_only_official_regulation')->comment('公式ルールに準拠したアカウントのみフォローを承認');
            $table->boolean('follow_back_only_set_icon')->comment('アイコンを設定しているアカウントのみフォロー');
            $table->integer('follow_back_only_tweets_more_than')->comment('最低ツイート数');
            $table->string('follow_back_only_profile_contains')->comment('プロフにこれらの言葉のどれかを含むアカウントのみフォロー（サロン垢ルール）');

            $table->timestamps();
        });

        Schema::table('configurations', function ($table) {
            // INDEX・外部キー
            $table->primary('account_id');
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
