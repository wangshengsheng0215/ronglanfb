<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectPlanningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_planning', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->string('project_name')->comment('项目名称');
            $table->string('project_type')->comment('项目类型');
            $table->tinyInteger('project_type_int')->comment('项目类型');
            $table->decimal('project_amount',10,2)->comment('项目薪酬');
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
        Schema::dropIfExists('project_planning');
    }
}
