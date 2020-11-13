<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Proouts;
use App\Models\Proplanning;
use App\Models\Prowhole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    //项目规划
    public function projectplanning(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    $rules = [
                        'project_name'=>'required',
                        'project_type'=>'required',
                        'project_amount'=>'required',
                        'project_introduce'=>'required',
                        'filename'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'project_name.required' => '项目名称不为空',
                        'project_type.required' => '项目类型不为空',
                        'project_amount.required' => '项目薪酬不为空',
                        'project_introduce.required' => '项目介绍不为空',
                        'filename.required' => '文件路径不为空',
                    ];

                    $this->validate($request, $rules, $messages);
                    $prop = new Proplanning();
                    $prop->project_name = $request->input('project_name');
                    $prop->project_type = $request->input('project_type');
                    $prop->project_amount = $request->input('project_amount');
                    $prop->project_introduce = $request->input('project_introduce');
                    $prop->filename = implode($request->input('filename'));
                    $uctype = config('userconfig.type_cn');
                    $k = array_search($request->input('project_type'),$uctype);
                    $prop->project_type_int = $k;
                      if( $prop->save()){
                          return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                      }
                        return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );

                }catch (ValidationException $validationException){
                    $messages = $validationException->validator->getMessageBag()->first();
                    return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

    //整包项目
    public function projectwhole(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    $rules = [
                        'project_name'=>'required',
                        'project_type'=>'required',
                        'project_amount'=>'required',
                        'project_introduce'=>'required',
                        'filename'=>'required',

                    ];
                    //自定义消息
                    $messages = [
                        'project_name.required' => '项目名称不为空',
                        'project_type.required' => '项目类型不为空',
                        'project_amount.required' => '项目薪酬不为空',
                        'project_introduce.required' => '项目介绍不为空',
                        'filename.required' => '文件路径不为空',

                    ];

                    $this->validate($request, $rules, $messages);
                    $prop = new Prowhole();
                    $prop->project_name = $request->input('project_name');
                    $prop->project_type = $request->input('project_type');
                    $prop->project_amount = $request->input('project_amount');
                    $prop->project_introduce = $request->input('project_introduce');
                    $prop->filename = implode($request->input('filename'));
                    $uctype = config('userconfig.type_cn');
                    $k = array_search($request->input('project_type'),$uctype);
                    $prop->project_type_int = $k;
                    $prop->is_kaip = $request->input('is_kaip')?$request->input('is_kaip'):2;
                    if( $prop->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );
                }catch (ValidationException $validationException){
                    $messages = $validationException->validator->getMessageBag()->first();
                    return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //外包项目
    public function projectoutsourcing(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    $rules = [
                        'project_name'=>'required',
                        'project_position'=>'required',
                        'skills_position'=>'required',
                        'starttime'=>'required',
                        'endtime'=>'required',
                        'project_amount'=>'required',
                        'project_introduce'=>'required',
                        'filename'=>'required',

                    ];
                    //自定义消息
                    $messages = [
                        'project_name.required' => '项目名称不为空',
                        'project_position.required' => '需求职位不为空',
                        'skills_position.required' => '技能需求不为空',
                        'starttime.required' => '开始时间不为空',
                        'endtime.required' => '结束时间不为空',
                        'project_amount.required' => '工作薪酬不为空',
                        'project_introduce.required' => '工作内容不为空',
                        'filename.required' => '文件路径不为空',

                    ];

                    $this->validate($request, $rules, $messages);
                    $prop = new Proouts();
                    $prop->project_name = $request->input('project_name');
                    $prop->project_position = $request->input('project_position');
                    $prop->skills_position = $request->input('skills_position');
                    $prop->starttime = $request->input('starttime');
                    $prop->endtime = $request->input('endtime');
                    $prop->project_amount = $request->input('project_amount');
                    $prop->project_introduce = $request->input('project_introduce');
                    $prop->filename = implode($request->input('filename'));
                    $uctype = config('userconfig.type_out');
                    $k = array_search($request->input('project_position'),$uctype);
                    $prop->project_position_int = $k;
                    if( $prop->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );
                }catch (ValidationException $validationException){
                    $messages = $validationException->validator->getMessageBag()->first();
                    return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //项目新建
    public function addproject(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                $project_class = $request->input('project_class');
                //1项目规划2整包项目3外包项目
                if($project_class == 1){
                    try {
                        $rules = [
                            'project_name'=>'required',
                            'project_type'=>'required',
                            'project_amount'=>'required',
                            'project_introduce'=>'required',
                            'filename'=>'required',
                        ];
                        //自定义消息
                        $messages = [
                            'project_name.required' => '项目名称不为空',
                            'project_type.required' => '项目类型不为空',
                            'project_amount.required' => '项目薪酬不为空',
                            'project_introduce.required' => '项目介绍不为空',
                            'filename.required' => '文件路径不为空',
                        ];

                        $this->validate($request, $rules, $messages);
                        $prop = new Project();
                        $prop->project_name = $request->input('project_name');
                        $prop->project_class = $project_class;
                        $prop->project_status = 1;
                        $prop->project_type = $request->input('project_type');
                        $prop->project_amount = $request->input('project_amount');
                        $prop->project_introduce = $request->input('project_introduce');
                        $prop->filename = implode($request->input('filename'));
                        $uctype = config('userconfig.type_cn');
                        $k = array_search($request->input('project_type'),$uctype);
                        $prop->project_type_int = $k;
                        if( $prop->save()){
                            return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                        }
                        return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );

                    }catch (ValidationException $validationException){
                        $messages = $validationException->validator->getMessageBag()->first();
                        return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                    }
                }elseif($project_class == 2){
                    try {
                        $rules = [
                            'project_name'=>'required',
                            'project_type'=>'required',
                            'project_amount'=>'required',
                            'project_introduce'=>'required',
                            'filename'=>'required',

                        ];
                        //自定义消息
                        $messages = [
                            'project_name.required' => '项目名称不为空',
                            'project_type.required' => '项目类型不为空',
                            'project_amount.required' => '项目薪酬不为空',
                            'project_introduce.required' => '项目介绍不为空',
                            'filename.required' => '文件路径不为空',

                        ];

                        $this->validate($request, $rules, $messages);
                        $prop = new Project();
                        $prop->project_name = $request->input('project_name');
                        $prop->project_class = $project_class;
                        $prop->project_status = 1;
                        $prop->project_type = $request->input('project_type');
                        $prop->project_amount = $request->input('project_amount');
                        $prop->project_introduce = $request->input('project_introduce');
                        $prop->filename = implode($request->input('filename'));
                        $uctype = config('userconfig.type_cn');
                        $k = array_search($request->input('project_type'),$uctype);
                        $prop->project_type_int = $k;
                        $prop->is_kaip = $request->input('is_kaip')?$request->input('is_kaip'):2;
                        if( $prop->save()){
                            return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                        }
                        return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );
                    }catch (ValidationException $validationException){
                        $messages = $validationException->validator->getMessageBag()->first();
                        return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                    }
                }elseif ($project_class == 3){
                    try {
                        $rules = [
                            'project_name'=>'required',
                            'skills_position'=>'required',
                            'starttime'=>'required',
                            'endtime'=>'required',
                            'project_amount'=>'required',
                            'project_introduce'=>'required',
                            'filename'=>'required',

                        ];
                        //自定义消息
                        $messages = [
                            'project_name.required' => '项目名称不为空',
                            'skills_position.required' => '技能需求不为空',
                            'starttime.required' => '开始时间不为空',
                            'endtime.required' => '结束时间不为空',
                            'project_amount.required' => '工作薪酬不为空',
                            'project_introduce.required' => '工作内容不为空',
                            'filename.required' => '文件路径不为空',

                        ];

                        $this->validate($request, $rules, $messages);
                        $prop = new Project();
                        $prop->project_name = $request->input('project_name');
                        $prop->project_class = $project_class;
                        $prop->project_status = 1;
                        $prop->skills_position = $request->input('skills_position');
                        $prop->starttime = $request->input('starttime');
                        $prop->endtime = $request->input('endtime');
                        $prop->project_amount = $request->input('project_amount');
                        $prop->project_introduce = $request->input('project_introduce');
                        $prop->filename = implode($request->input('filename'));
                        if( $prop->save()){
                            return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                        }
                        return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );
                    }catch (ValidationException $validationException){
                        $messages = $validationException->validator->getMessageBag()->first();
                        return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                    }
                }else{
                    return json_encode(['errcode'=>'4008','errmsg'=>'没有该分类项目创建'],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

}
