<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBigCatTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_cat_types', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('big_cat_type_name')->nullable()->comment('ネコ種別');
            $table->integer('sort_no')->nullable()->default(0)->comment('順番');
            $table->boolean('delete_flg')->nullable()->default(false)->comment('無効フラグ');
            $table->integer('update_user_id')->nullable()->comment('更新ユーザーID');
            $table->string('ip_addr', 40)->nullable()->comment('IPアドレス');
            $table->dateTime('created_at')->nullable()->comment('生成日時');
            $table->dateTime('updated_at')->nullable()->useCurrent()->comment('更新日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('big_cat_types');
    }
}
