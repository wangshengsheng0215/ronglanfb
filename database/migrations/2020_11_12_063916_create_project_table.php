<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->string('project_name')->comment('项目名称');
            $table->tinyInteger('project_status')->comment('项目状态');
            $table->tinyInteger('project_class')->comment('分类 1项目规划2整包项目3外包项目');
            $table->string('project_type')->nullable()->comment('项目类型');
            $table->tinyInteger('project_type_int')->nullable()->comment('项目类型');
            $table->string('skills_position')->nullable()->comment('技能需求');
            $table->date('starttime')->nullable()->comment('开始时间');
            $table->date('endtime')->nullable()->comment('结束时间');
            $table->decimal('project_amount',10,2)->comment('项目薪酬');
            $table->tinyInteger('is_kaip')->nullable()->comment('是否开票');
            $table->longText('project_introduce')->comment('项目介绍');
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
        Schema::dropIfExists('project');
    }
}
