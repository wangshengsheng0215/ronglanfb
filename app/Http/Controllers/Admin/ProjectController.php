<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    //所有项目
    public function projectlist(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'prostatus'=>'required',
                    'proclass'=>'required',

                ];
                //自定义消息
                $messages = [
                    'prostatus.required' => '审核标识不为空',
                    'proclass.required' => '列表标识不为空',

                ];
                $this->validate($request, $rules, $messages);
                $prostatus = $request->input('prostatus');
                $proclass = $request->input('proclass');
                $paginate = $request->input('paginate')?$request->input('paginate'):10;
                $sreach = $request->input('sreach');
                $where = '1=1';
                if(!empty($sreach)){
                    $where .= ' and project_name like "%' . $sreach . '%"';
                }
                $protype = $request->input('protype');
                if(!empty($protype)){
                    $where .= " and project_type = '{$protype}'";
                }
                if($prostatus == 1){
                    switch ($proclass){
                        case 4:
                            $list = Project::whereRaw($where)->where('project_status',$prostatus)->whereIn('project_class',[1,2,3])->orderBy('addtime','desc')->paginate($paginate);
                            break;
                        default:
                            $list = Project::whereRaw($where)->where('project_status',$prostatus)->where('project_class',$proclass)->orderBy('addtime','desc')->paginate($paginate);
                    }
                }elseif ($prostatus == 2){
                    switch ($proclass){
                        case 4:
                            $list = Project::whereRaw($where)->where('project_status','<>',1)->whereIn('project_class',[1,2,3])->orderBy('addtime','desc')->paginate($paginate);
                            break;
                        default:
                            $list = Project::whereRaw($where)->where('project_status','<>',1)->where('project_class',$proclass)->orderBy('addtime','desc')->paginate($paginate);
                    }
                }else{
                    return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>[]],JSON_UNESCAPED_UNICODE );
                }
                return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>$list],JSON_UNESCAPED_UNICODE );
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }
    //项目规划

}
