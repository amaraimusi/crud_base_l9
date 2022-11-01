<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallDogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('small_dogs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('dog_val')->nullable()->comment('イヌ数値');
            $table->string('small_dog_name')->nullable()->comment('子犬名');
            $table->date('small_dog_date')->nullable()->comment('子犬日付');
            $table->integer('dog_type')->nullable()->comment('犬種');
            $table->dateTime('dog_dt')->nullable()->comment('子犬保護日時');
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
        Schema::dropIfExists('small_dogs');
    }
}
