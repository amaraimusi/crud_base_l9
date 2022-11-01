<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNekosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nekos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('neko_val')->nullable();
            $table->string('neko_name')->nullable();
            $table->date('neko_date')->nullable();
            $table->integer('neko_type')->nullable()->comment('猫種別');
            $table->dateTime('neko_dt')->nullable();
            $table->tinyInteger('neko_flg')->nullable()->default(0)->comment('ネコフラグ');
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
        Schema::dropIfExists('nekos');
    }
}
