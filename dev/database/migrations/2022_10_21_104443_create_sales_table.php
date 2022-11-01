<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('client_id')->nullable()->comment('顧客ID');
            $table->decimal('sales_amt', 10, 0)->nullable()->comment('売上額');
            $table->string('status', 16)->nullable()->comment('ステータス');
            $table->date('billing_date')->nullable()->comment('請求日');
            $table->decimal('billing_amt', 10, 0)->nullable()->comment('請求額');
            $table->date('payment_date')->nullable()->comment('入金日');
            $table->decimal('payment_amt', 10, 0)->nullable()->comment('入金額');
            $table->decimal('commission', 10, 0)->nullable()->comment('手数料');
            $table->decimal('tax', 10, 0)->nullable()->comment('消費税');
            $table->string('note', 2000)->nullable()->comment('備考');
            $table->double('sort_no')->nullable()->default(0)->comment('順番');
            $table->boolean('delete_flg')->nullable()->default(false)->comment('無効フラグ');
            $table->integer('update_user_id')->nullable()->comment('更新者');
            $table->string('ip_addr', 40)->nullable()->comment('IPアドレス');
            $table->dateTime('created_at')->nullable()->comment('生成日時');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('更新日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
