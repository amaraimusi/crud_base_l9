<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBigCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_cats', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('big_cat_name')->nullable()->comment('ネコ名');
            $table->date('public_date')->nullable()->comment('公開日');
            $table->integer('big_cat_type')->nullable()->comment('有名猫種別');
            $table->decimal('price', 11, 0)->nullable()->comment('価格');
            $table->integer('subsc_count')->comment('サブスク数');
            $table->dateTime('work_dt')->nullable()->comment('作業日時');
            $table->tinyInteger('big_cat_flg')->nullable()->default(0)->comment('ネコフラグ');
            $table->string('img_fn', 256)->nullable()->comment('画像ファイル名');
            $table->text('note')->nullable()->comment('備考');
            $table->integer('sort_no')->nullable()->default(0)->comment('順番');
            $table->boolean('delete_flg')->nullable()->default(false)->comment('無効フラグ');
            $table->integer('update_user_id')->nullable()->comment('更新者');
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
        Schema::dropIfExists('big_cats');
    }
}
