<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kd9703\Constants\ShowNew;

class AddConfigurationNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->integer('show_new_by')->comment('新着表示基準')->default(ShowNew::BY_CREATED_AT)->after('follow_back_only_profile_contains');
            $table->integer('show_new_days')->comment('新着表示の日数')->default(14)->after('show_new_by');;

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
            $table->dropColumn('show_new_by');
            $table->dropColumn('show_new_days');
        });
    }
}
