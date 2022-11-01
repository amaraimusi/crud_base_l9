<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('ユーザー名/アカウント名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('Eメール検証済時刻(Laravel内部処理用)');
            $table->string('nickname', 50)->nullable()->comment('名前');
            $table->string('password')->comment('パスワード');
            $table->rememberToken()->comment('維持用トークン(Laravel内部処理用)');
            $table->enum('role', ['master', 'developer', 'admin', 'client', 'oparator'])->nullable()->comment('権限');
            $table->string('temp_hash', 50)->nullable()->comment('仮登録ハッシュコード(Laravel内部処理用)');
            $table->dateTime('temp_datetime')->nullable()->comment('仮登録制限時刻(Laravel内部処理用)');
            $table->integer('sort_no')->nullable()->default(0)->comment('順番');
            $table->boolean('delete_flg')->nullable()->default(false)->comment('削除フラグ');
            $table->integer('update_user_id')->nullable()->comment('更新ユーザーID');
            $table->string('ip_addr', 40)->nullable()->comment('更新IPアドレス');
            $table->dateTime('created_at')->nullable()->comment('生成日時B');
            $table->dateTime('updated_at')->nullable()->comment('更新日時B');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
