<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectOutsourcingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_outsourcing', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->string('project_name')->comment('项目名称');
            $table->string('project_position')->comment('需求职位');
            $table->tinyInteger('project_position_int')->comment('需求职位');
            $table->string('skills_position')->comment('技能需求');
            $table->date('starttime')->comment('开始时间');
            $table->date('endtime')->comment('结束时间');
            $table->decimal('project_amount',10,2)->comment('工作薪酬');
            $table->longText('project_introduce')->comment('工作内容');
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
        Schema::dropIfExists('project_outsourcing');
    }
}
