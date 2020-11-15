<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\Shenhelog;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
 //认证
    public function Certificate(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'userstatus'=>'required',
                    'userclass'=>'required',

                ];
                //自定义消息
                $messages = [
                    'userstatus.required' => '认证标识不为空',
                    'userclass.required' => '列表标识不为空',

                ];
                $this->validate($request, $rules, $messages);

                $userstatus = $request->input('userstatus');
                $userclass = $request->input('userclass');
                $paginate = $request->input('paginate')?$request->input('paginate'):10;
                $sreach = $request->input('sreach');
                $where = '1=1';
                if(!empty($sreach)){
                    if($userclass == 4){
                        $where .= ' and enterprise_name like "%' . $sreach . '%"';
                    }else{
                        $where .= ' and name like "%' . $sreach . '%"';
                    }
                }
                if($userstatus == 1){
                    switch ($userclass){
                        case 2:
                            $list = Users::whereRaw($where)->where('certification_type',1)->whereIn('certification_shenhe',[2,4])->paginate($paginate);
                            break;
                        case 3:
                            $list = DB::table('users')
                                ->join('programmer','users.id','=','programmer.uid')
                                    ->whereRaw($where)->where('certification_type',2)->whereIn('certification_shenhe',[5,7])->paginate($paginate);
                            foreach ($list as $k=>$v){
                                $v->filearray = [];
                                $v->filearray = explode(',',$v->filename);
                            }
                            break;
                        case 4:
                            $list = DB::table('users')
                                ->join('enterprise','users.id','=','enterprise.uid')
                                ->whereRaw($where)->where('certification_type',2)->whereIn('certification_shenhe',[8,10])->paginate($paginate);
                            foreach ($list as $k=>$v){
                                $v->filearray = [];
                                $v->filearray = explode(',',$v->filename);
                            }
                            break;
                    }

                }elseif($userstatus == 2){
                    switch ($userclass){
                        case 2:
                            $list = DB::table('users')
                                ->join('shenhelog','users.id', '=','shenhelog.userid')
                                ->whereRaw($where)->where('certification_type',2)->whereIn('certification_shenhe',[3])->paginate($paginate);
                            break;
                        case 3:
                            $list = DB::table('users')
                                ->join('programmer','users.id','=','programmer.uid')
                                ->join('shenhelog','users.id', '=','shenhelog.userid')
                                ->whereRaw($where)->where('certification_type',3)->whereIn('certification_shenhe',[6])->paginate($paginate);
                            foreach ($list as $k=>$v){
                                $v->filearray = [];
                                $v->filearray = explode(',',$v->filename);
                            }
                            break;
                        case 4:
                            $list = DB::table('users')
                                ->join('enterprise','users.id','=','enterprise.uid')
                                ->join('shenhelog','users.id', '=','shenhelog.userid')
                                ->whereRaw($where)->where('certification_type',4)->whereIn('certification_shenhe',[9])->paginate($paginate);
                            foreach ($list as $k=>$v){
                                $v->filearray = [];
                                $v->filearray = explode(',',$v->filename);
                            }
                            break;
                    }
                }else{
                    return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>[]],JSON_UNESCAPED_UNICODE );
                }
              return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>$list],JSON_UNESCAPED_UNICODE);
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

    //审核接口
    public function shenhe(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'shenhestatus'=>'required',
                    'userid'=>'required',

                ];
                //自定义消息
                $messages = [
                    'shenhestatus.required' => '审核状态不为空',
                    'userid.required' => '用户id不为空',

                ];
                $this->validate($request, $rules, $messages);
                $shenhestatus = $request->input('shenhestatus');
                $userid = $request->input('userid');
                //个人认证 通过3不通过4  程序员认证通过6 不通过7 企业认证通过9 不通过10
                if($shenhestatus == 3){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>3,'certification_type'=>2,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }elseif($shenhestatus == 4){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>4,'certification_type'=>1,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }elseif ($shenhestatus == 6){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>6,'certification_type'=>3,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }elseif ($shenhestatus == 7){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>7,'certification_type'=>2,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }elseif ($shenhestatus == 9){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>9,'certification_type'=>4,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }elseif ($shenhestatus == 10){
                    $a = Users::where('id',$userid)->update(['certification_shenhe'=>10,'certification_type'=>2,'certification_status'=>1]);
                    $shenlog = new Shenhelog();
                    $shenlog->userid = $userid;
                    $shenlog->shenheid = $user->id;
                    $shenlog->beizhu = $request->input('beizhu');
                    $shenlog->save();
                }else{
                    return json_encode(['errcode'=>'209','errmsg'=>'传值不对'],JSON_UNESCAPED_UNICODE );
                }
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

    //专属客服
    public function clientlist(Request $request){
        $user = \Auth::user();
        if($user){
            $paginate = $request->input('paginate')?$request->input('paginate'):10;
            $sreach = $request->input('sreach');
            $where = '1=1';
            if(!empty($sreach)){
                    $where .= ' and name like "%' . $sreach . '%"';

            }
            $list = Client::whereRaw($where)->paginate($paginate);
            return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>$list],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

    //联系信息填写
    public function clientupdate(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                $rules = [
                    'id'=>'required',
                    'name'=>'required',
                    'companyname'=>'required',
                    'mobile'=>'required',
                    'email'=>'required',
                    'fuwu'=>'required',
                    'xuqiu'=>'required',
                ];
                //自定义消息
                $messages = [
                    'id.required' => 'id不为空',
                    'name.required' => '姓名不为空',
                    'companyname.required' => '公司名称不为空',
                    'mobile.required' => '手机号不为空',
                    'email.required' => '邮箱不为空',
                    'fuwu.required' => '服务不为空',
                    'xuqiu.required' => '需求不为空',

                ];
                $this->validate($request, $rules, $messages);
                $clientid = $request->input('id');
                $client = Client::where('id',$clientid)->first();
                $client->name = $request->input('name');
                $client->companyname = $request->input('companyname');
                $client->mobile = $request->input('mobile');
                $client->email = $request->input('email');
                $client->fuwu = $request->input('fuwu');
                $client->xuqiu = $request->input('xuqiu');
                $client->status = 2;
                if($client->save()){
                    return json_encode(['errcode'=>'1','errmsg'=>'提交成功'],JSON_UNESCAPED_UNICODE );
                }
                return json_encode(['errcode'=>'201','errmsg'=>'提交失败'],JSON_UNESCAPED_UNICODE );

            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }

            }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

   //认证过的人员名单
    public function userlist(Request $request){
        $paginate = $request->input('paginate')?$request->input('paginate'):10;
        $list = Users::whereIn('certification_type',[3,4])->whereIn('certification_shenhe',[6,9])->paginate($paginate);
        return json_encode(['errcode'=>'1','errmsg'=>'ok','data'=>$list],JSON_UNESCAPED_UNICODE);
    }
}
