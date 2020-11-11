<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owner_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('level');
            $table->text('message');
            $table->text('context');

            $table->integer('job_id')->unsigned()->nullable();
            $table->string('account_id');

            $table->string('instance');
            $table->integer('remote_addr')->nullable()->unsigned();
            $table->string('user_agent')->nullable();
            $table->integer('pid')->unsigned();

            $table->dateTime('created_at');

            // インデックス
            $table->index(['created_at']);
            $table->index(['account_id', 'job_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('owner_logs');
    }
}
