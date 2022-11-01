<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('neko_val')->nullable()->default(100)->comment('ネコ数値');
            $table->string('neko_name')->nullable()->comment('ネコ名');
            $table->date('neko_date')->nullable()->comment('ネコ日付');
            $table->integer('neko_group')->default(0)->comment('ネコ種別');
            $table->dateTime('neko_dt')->nullable()->comment('ネコ日時');
            $table->boolean('neko_flg')->default(false)->comment('ネコフラグ');
            $table->string('img_fn')->nullable()->comment('画像ファイル名');
            $table->text('note')->nullable()->comment('備考');
            $table->integer('sort_no')->nullable()->default(0)->comment('順番');
            $table->boolean('delete_flg')->nullable()->default(false)->comment('無効フラグ');
            $table->integer('update_user_id')->nullable()->comment('更新者ユーザーID');
            $table->string('ip_addr', 40)->nullable()->comment('IPアドレス');
            $table->dateTime('created_at')->useCurrent()->comment('生成日時');
            $table->dateTime('updated_at')->useCurrent()->comment('更新日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animals');
    }
}
