<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id')->comment('主键id');
            $table->string('username')->unique()->comment('用户名');
            $table->string('mobile',13)->unique()->comment('手机号');
            $table->string('password')->comment('密码');
            $table->string('remember_token')->nullable()->comment('token');
            $table->tinyInteger('status')->comment('状态');
            $table->tinyInteger('role')->comment('用户权限');
            $table->string('name')->nullable()->comment('真实姓名');
            $table->string('card_id',18)->nullable()->comment('身份证号');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('zfb_Alipay')->nullable()->comment('支付宝账号');
            $table->text('personal_profile')->nullable()->comment('简介');
            $table->string('card_file')->nullable()->comment('扫描件');
            $table->string('head_portrait')->nullable()->comment('头像');
            $table->tinyInteger('certification_status')->comment('认证状态 1为未认证 2为已认证');
            $table->tinyInteger('certification_type')->comment('认证类型 1没认证 2个人认证 3程序员认证 4企业认证');
            $table->tinyInteger('is_project')->comment('1否 2是');
            $table->string('project_cn')->nullable()->comment('承接项目类型');
            $table->string('project_int')->nullable()->comment('承接项目类型int');
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
        Schema::dropIfExists('users');
    }
}
