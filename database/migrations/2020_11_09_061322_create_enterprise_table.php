<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnterpriseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enterprise', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->increments('id');
            $table->tinyInteger('uid')->comment('用户id');
            $table->string('enterprise_name')->comment('企业名称');
            $table->string('enterprise_homepage')->comment('企业主页');
            $table->string('enterprise_address')->comment('企业地址');
            $table->string('enterprise_people')->comment('企业代理人');
            $table->string('type_cn')->comment('接单方向');
            $table->string('type_int')->comment('接单方向');
            $table->text('enterprise_Introduction')->comment('企业简介');
            $table->string('filename')->comment('文件路径');
            $table->timestamp('addtime')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('添加时间');
            $table->timestamp('updatetime')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('修改时间');
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
        Schema::dropIfExists('enterprise');
    }
}
