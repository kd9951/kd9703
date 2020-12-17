<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddPostsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_recipients', function ($table) {
            // INDEX・外部キー
            $table->index(['from_account_id', 'to_account_id', 'posted_at'], 'IDX_FROM_TO_POSTED_AT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_recipients', function ($table) {
            $table->dropIndex('IDX_FROM_TO_POSTED_AT');
        });
    }
}
