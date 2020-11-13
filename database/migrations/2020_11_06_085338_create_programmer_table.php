<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgrammerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmer', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->increments('id');
            $table->tinyInteger('uid')->comment('用户id');
            $table->string('qualifications')->comment('学历');
            $table->string('skills')->comment('技能');
            $table->string('experience')->comment('工作经验');
            $table->string('company')->comment('公司');
            $table->string('workstatus')->comment('工作状态');
            $table->string('starttime')->comment('开始时间');
            $table->string('endtime')->comment('结束时间');
            $table->time('starthour')->comment('开始小时');
            $table->time('endhour')->comment('结束小时');
            $table->string('type_cn')->comment('接单方向');
            $table->string('type_int')->comment('接单方向');
            $table->decimal('dayamount',10,2)->comment('天金额');
            $table->decimal('monthamount',10,2)->comment('月金额');
            $table->text('projectex')->comment('项目经历');
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
        Schema::dropIfExists('programmer');
    }
}
