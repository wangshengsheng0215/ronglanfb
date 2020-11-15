<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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


    //项目审核
    public function projectshenhe(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'shenhestatus'=>'required',
                    'proid'=>'required',

                ];
                //自定义消息
                $messages = [
                    'shenhestatus.required' => '审核状态不为空',
                    'proid.required' => '项目id不为空',

                ];
                $this->validate($request, $rules, $messages);
                $proid = $request->input('proid');
                $shenhestatus = $request->input('shenhestatus');
                $a = Project::where('id',$proid)->update(['project_status'=>$shenhestatus]);
                if ($a){
                    return json_encode(['errcode'=>'1','errmsg'=>'审核成功'],JSON_UNESCAPED_UNICODE );
                }else{
                    return json_encode(['errcode'=>'201','errmsg'=>'审核不成功'],JSON_UNESCAPED_UNICODE );
                }
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //项目推送
    public function projectsend(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'proid'=>'required',
                    'uid'=>'required',
                ];
                //自定义消息
                $messages = [
                    'proid.required' => '项目id不为空',
                    'uid.required' => '推送人员不为空',
                ];
                $this->validate($request, $rules, $messages);
                $proid = $request->input('proid');
                $uid = $request->input('uid');
                $data = [];
                foreach ($uid as $k=>$v){
                    $data[$k]['proid'] = $proid;
                    $data[$k]['uid'] = $v;
                    $data[$k]['status'] = 1;
                }
               $a = DB::table('projectsend')->insert($data);
                if ($a){
                    return json_encode(['errcode'=>'1','errmsg'=>'推送成功'],JSON_UNESCAPED_UNICODE );
                }else{
                    return json_encode(['errcode'=>'201','errmsg'=>'推送不成功'],JSON_UNESCAPED_UNICODE );
                }
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

}
