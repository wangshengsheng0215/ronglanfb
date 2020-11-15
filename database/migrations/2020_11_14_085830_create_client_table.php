<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->string('name')->comment('客户姓名');
            $table->string('mobile')->comment('手机号');
            $table->tinyInteger('status')->comment('联系状态');
            $table->string('companyname')->nullable()->comment('客户公司');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('fuwu')->nullable()->comment('所需服务');
            $table->text('xuqiu')->nullable()->comment('需求');
            $table->timestamp('addtime')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('添加时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client');
    }
}
