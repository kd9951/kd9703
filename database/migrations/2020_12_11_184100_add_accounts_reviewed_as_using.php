<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAccountsReviewedAsUsing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->datetime('reviewed_as_using_user_at')->comment('アプリ利用者として情報取得した日時')->nullable()->after('reviewed_at');
            $table->datetime('status_updated_at')->comment('ポスト・フォロー・フォロワー・いいね数が最後に増えた日時')->nullable()->after('reviewed_as_using_user_at');

            $table->index('reviewed_as_using_user_at');
            // $table->index(['oauth_access_token', 'reviewed_as_using_user_at'], 'IDX_USING_UPDATED');
            $table->index(['is_salon_account', 'status_updated_at'], 'IDX_ACTIVE');
        });
        DB::statement('ALTER TABLE `accounts` ADD INDEX `IDX_USING_UPDATED` (`oauth_access_token`(64), `reviewed_as_using_user_at`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('IDX_ACTIVE');
            $table->dropIndex('IDX_USING_UPDATED');
            $table->dropColumn('reviewed_as_using_user_at');
            $table->dropColumn('status_updated_at');
        });
    }
}
