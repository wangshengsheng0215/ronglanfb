<?php

namespace App\Http\Controllers\Api;

use App\Models\Enterprise;
use App\Models\Programmer;
use App\Models\Users;
use App\Service\ImageUploadhandler;
use App\Service\Sms\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
    public function userlist(Request $request){
        //$user = \Auth::user();
        $user = auth('api')->user();
        dd($user);
    }

    //修改密码
    public function updatepassword(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                //规则
                $rules = [
                    'oldpassword'=>'required',
                    'newpassword'=>'required',
                ];
                //自定义消息
                $messages = [
                    'oldpassword.required' => '请输入旧密码',
                    'newpassword.required' => '请输入新密码',
                ];

                $this->validate($request, $rules, $messages);

                $oldpassword = $request->input('oldpassword');
                $newpassword = $request->input('newpassword');

                if(Hash::check($oldpassword,$user->password)){
                    $user->password = Hash::make($newpassword);
                    if($user->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'更新成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'更新失败'],JSON_UNESCAPED_UNICODE );
                }else{
                    return json_encode(['errcode'=>'1003','errmsg'=>'旧密码不对'],JSON_UNESCAPED_UNICODE );
                }

            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //上传头像
    public function headuploads(Request $request,ImageUploadhandler $uploadhandler){
        $user = \Auth::user();
        if($user){
            try {
                //规则
                $rules = [
                    'headfile'=>'required',
                    ];
                    //自定义消息
                $messages = [
                    'headfile.required' => '文件不为空',
                    ];

                $this->validate($request, $rules, $messages);
                $headfile = $request->file('headfile');
                $result = $uploadhandler->save($headfile,'headport',$user->id);
                if($result){
                    $data['headport'] = $result['path'];
                    $path = strstr($data['headport'],'uploads');
                    return json_encode(['errcode'=>'1','errmsg'=>'上传成功','data'=>$path],JSON_UNESCAPED_UNICODE );
                }
                 return json_encode(['errcode'=>'1','errmsg'=>'上传失败'],JSON_UNESCAPED_UNICODE );
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }

    }


    //修改头像
    public function updatehead(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                //规则
                $rules = [
                    'head_portrait'=>'required',
                ];
                //自定义消息
                $messages = [
                    'head_portrait.required' => '头像路径不为空',
                ];

                $this->validate($request, $rules, $messages);
                $user->head_portrait = $request->head_portrait;
                if($user->save()){
                    return json_encode(['errcode'=>'1','errmsg'=>'更新成功'],JSON_UNESCAPED_UNICODE );
                }
                return json_encode(['errcode'=>'201','errmsg'=>'更新失败'],JSON_UNESCAPED_UNICODE );

            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

    //修改邮箱
    public function updateemail(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    //规则
                    $rules = [
                        'oldemail'=>'required',
                        'oldemailcode'=>'required',
                        'newemail'=>'required',
                        'newemailcode'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'oldemail.required' => '初始邮箱不为空',
                        'oldemailcode.required' => '初始邮箱验证码不为空',
                        'newemail.required' => '新邮箱不为空',
                        'newemailcode.required' => '新邮箱验证码不为空',
                    ];

                    $this->validate($request, $rules, $messages);
                    $oldemail = $request->input('oldemail');
                    $oldemailcode = $request->input('oldemailcode');
                    $newemail = $request->input('newemail');
                    $newemailcode = $request->input('newemailcode');

                    if(!(Users::where('id',$user->id)->where('email',$oldemail)->first())){
                        return json_encode(['errcode'=>'4004','errmsg'=>'初始邮箱不对应'],JSON_UNESCAPED_UNICODE );
                    }
                    $checkcode1 = Sms::checkCode($oldemail,$oldemailcode);
                    if(is_array($checkcode1)){
                        return json_encode($checkcode1,JSON_UNESCAPED_UNICODE);
                    }
                    $checkcode = Sms::checkCode($newemail,$newemailcode);
                    if(is_array($checkcode)){
                        return json_encode($checkcode,JSON_UNESCAPED_UNICODE);
                    }
                    $user->email = $newemail;
                    if($user->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'更新成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'更新失败'],JSON_UNESCAPED_UNICODE );

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

    //修改支付宝账号
    public function updatepay(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    //规则
                    $rules = [
                        'oldpay'=>'required',
                        'mobile'=>'required',
                        'mobilecode'=>'required',
                        'email'=>'required',
                        'emailcode'=>'required',
                        'newpay'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'oldpay.required' => '旧支付账号不为空',
                        'mobile.required' => '手机号不为空',
                        'mobilecode.required' => '手机号验证码不为空',
                        'email.required' => '邮箱不为空',
                        'emailcode.required' => '邮箱验证码不为空',
                        'newpay.required' => '新支付账号不为空',
                    ];

                    $this->validate($request, $rules, $messages);
                    $oldpay = $request->input('oldpay');
                    $mobile = $request->input('mobile');
                    $mobilecode = $request->input('mobilecode');
                    $email = $request->input('email');
                    $emailcode = $request->input('emailcode');
                    $newpay = $request->input('newpay');
                    if(!(Users::where('id',$user->id)->where('zfb_Alipay',$oldpay)->first())){
                        return json_encode(['errcode'=>'4004','errmsg'=>'旧支付账号不对应'],JSON_UNESCAPED_UNICODE );
                    }
                    if(!(Users::where('id',$user->id)->where('mobile',$mobile)->first())){
                        return json_encode(['errcode'=>'4004','errmsg'=>'手机号不对应'],JSON_UNESCAPED_UNICODE );
                    }

                    if(!(Users::where('id',$user->id)->where('email',$email)->first())){
                        return json_encode(['errcode'=>'4004','errmsg'=>'邮箱不对应'],JSON_UNESCAPED_UNICODE );
                    }
                    $checkcode1 = Sms::checkCode($mobile,$mobilecode);
                    if(is_array($checkcode1)){
                        return json_encode($checkcode1,JSON_UNESCAPED_UNICODE);
                    }
                    $checkcode = Sms::checkCode($email,$emailcode);
                    if(is_array($checkcode)){
                        return json_encode($checkcode,JSON_UNESCAPED_UNICODE);
                    }
                    $user->zfb_Alipay = $newpay;
                    if($user->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'更新成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'更新失败'],JSON_UNESCAPED_UNICODE );
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


    //上传文件
    public function fileuploads(Request $request){
        $user = \Auth::user();
        if($user){
            try {
                //规则
                $rules = [
                    'file'=>'required',
                ];
                //自定义消息
                $messages = [
                    'file.required' => '文件不为空',
                ];

                $this->validate($request, $rules, $messages);

                $file = $request->file('file');
                if($file->isValid()){
                    $extension = $file->getClientOriginalExtension();
                    $folder_name = 'uploads/cffile/'. date("Ym/d", time());
                    $destinationPath = public_path() . '/' .$folder_name;
                    $fileName=$user->id . '_' . time() . '_' . str_random(10) . '.' . $extension;
                    $file->move($destinationPath,$fileName);
                    $path = "$folder_name/$fileName";
                    return json_encode(['errcode'=>'1','errmsg'=>'上传成功','data'=>$path],JSON_UNESCAPED_UNICODE );
                }
                return json_encode(['errcode'=>'1','errmsg'=>'上传失败'],JSON_UNESCAPED_UNICODE );
            }catch (ValidationException $validationException){
                $messages = $validationException->validator->getMessageBag()->first();
                return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //认证（个人认证）
    public function PersonalCertificate(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_type == 1 && $user->certification_status == 1 ){
                try {
                    //规则/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/
                    $rules = [
                        'name'=>'required',
                        'card_id'=>'required|identitycards',
                        'email'=>'required|email',
                        'emailcode'=>'required',
                        'zfb_Alipay'=>'required',
                        'personal_profile'=>'required',
                        'card_file'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'name.required' => '真实姓名不为空',
                        'card_id.required' => '身份证号不为空',
                        'email.required' => '邮箱不为空',
                        'emailcode.required' => '邮箱验证码不为空',
                        'zfb_Alipay.required' => '支付宝账号不为空',
                        'personal_profile.required' => '简介不为空',
                        'card_file.required' => '文件不为空',
                        'card_id.identitycards' => '身份证号不合法',
                        'email.email' => '邮箱不合法',
                    ];

                    $this->validate($request, $rules, $messages);
                    $name = $request->input('name');
                    $card_id = $request->input('card_id');
                    $zfb_Alipay = $request->input('zfb_Alipay');
                    $personal_profile = $request->input('personal_profile');
                    $card_file = $request->input('card_file');
                    $email = $request->input('email');
                    $code = $request->input('emailcode');
                    $checkcode = Sms::checkCode($email,$code);
                    if(is_array($checkcode)){
                        return json_encode($checkcode,JSON_UNESCAPED_UNICODE);
                    }

                    $user->name = $name;
                    $user->card_id = $card_id;
                    $user->email = $email;
                    $user->zfb_Alipay = $zfb_Alipay;
                    $user->personal_profile = $personal_profile;
                    $user->card_file = $card_file;
                    $user->certification_status = 2;
                    $user->certification_type = 1;
                    $user->certification_shenhe = 2;
                    if($user->save()){
                        return json_encode(['errcode'=>'1','errmsg'=>'更新成功'],JSON_UNESCAPED_UNICODE );
                    }
                    return json_encode(['errcode'=>'201','errmsg'=>'更新失败'],JSON_UNESCAPED_UNICODE );


                }catch (ValidationException $validationException){
                    $messages = $validationException->validator->getMessageBag()->first();
                    return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'已个人认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //认证（程序员认证）
    public function ProgrammerCertificate(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 1){
                if($user->certification_type == 2){
                    try {
                        //规则/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/
                        $rules = [
                            'qualifications'=>'required',
                            'skills'=>'required',
                            'experience'=>'required',
                            'company'=>'required',
                            'workstatus'=>'required',
                            'starttime'=>'required',
                            'endtime'=>'required',
                            'starthour'=>'required',
                            'endhour'=>'required',
                            'type_cn'=>'required',
                            'dayamount'=>'required',
                            'monthamount'=>'required',
                            'projectex'=>'required',
                            'filename'=>'required',
                        ];
                        //自定义消息
                        $messages = [
                            'qualifications.required' => '学历不为空',
                            'skills.required' => '擅长技能不为空',
                            'experience.required' => '工作经验不为空',
                            'company.required' => '曾在公司不为空',
                            'workstatus.required' => '工作状态不为空',
                            'starttime.required' => '开始日期不为空',
                            'endtime.required' => '结束日期不为空',
                            'starthour.required' => '开始时间不为空',
                            'endhour.required' => '结束时间不为空',
                            'type_cn.required' => '接单方向不为空',
                            'dayamount.required' => '天薪酬不为空',
                            'monthamount.required' => '月薪酬不为空',
                            'projectex.required' => '项目经历不为空',
                            'filename.required' => '文件名不为空',
                        ];

                        $this->validate($request, $rules, $messages);

                        $programmer = new Programmer();
                        $programmer->uid = $user->id;
                        $programmer->qualifications = $request->input('qualifications');
                        $programmer->skills = $request->input('skills');
                        $programmer->experience = $request->input('experience');
                        $programmer->company = $request->input('company');
                        $programmer->workstatus = $request->input('workstatus');
                        $programmer->starttime = $request->input('starttime');
                        $programmer->endtime = $request->input('endtime');
                        $programmer->starthour = $request->input('starthour');
                        $programmer->endhour = $request->input('endhour');
                        $programmer->type_cn = implode(',',$request->input('type_cn'));
                        $programmer->dayamount = $request->input('dayamount');
                        $programmer->monthamount = $request->input('monthamount');
                        $programmer->projectex = $request->input('projectex');
                        $programmer->filename = $request->input('filename');
                        $uctype = config('userconfig.type_cn');
                        $str = '';
                        foreach ($request->input('type_cn') as $v){
                            $k = array_search($v,$uctype);
                            $str .= $k.',';
                        }
                        $programmer->type_int = rtrim($str,',');


                        if($programmer->save()){
                            Users::where('id',$programmer->uid)->update(['certification_shenhe'=>5,'certification_type'=>2,'certification_status'=>2]);
                            return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                        }
                        return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );

                    }catch (ValidationException $validationException){
                        $messages = $validationException->validator->getMessageBag()->first();
                        return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                    }
                }elseif($user->certification_type == 4){
                    return json_encode(['errcode'=>'4003','errmsg'=>'已企业认证'],JSON_UNESCAPED_UNICODE );
                }elseif($user->certification_type == 3){
                    return json_encode(['errcode'=>'4003','errmsg'=>'已程序员认证'],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未审核/未通过'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


    //认证（企业认证）
    public function Enterprisecertificate(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 1){
                if($user->certification_type == 2){
                    try {
                    $rules = [
                        'enterprise_name'=>'required',
                        'enterprise_homepage'=>'required',
                        'enterprise_address'=>'required',
                        'enterprise_people'=>'required',
                        'type_cn'=>'required',
                        'enterprise_Introduction'=>'required',
                        'filename'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'enterprise_name.required' => '企业名称不为空',
                        'enterprise_homepage.required' => '企业主页不为空',
                        'enterprise_address.required' => '企业地址不为空',
                        'enterprise_people.required' => '企业代理人不为空',
                        'type_cn.required' => '接单方向不为空',
                        'enterprise_Introduction.required' => '企业简介不为空',
                        'filename.required' => '文件名不为空',
                    ];

                    $this->validate($request, $rules, $messages);
                    $enterprise = new Enterprise();
                    $enterprise->uid = $user->id;
                    $enterprise->enterprise_name = $request->input('enterprise_name');
                    $enterprise->enterprise_homepage = $request->input('enterprise_homepage');
                    $enterprise->enterprise_address = $request->input('enterprise_address');
                    $enterprise->enterprise_people = $request->input('enterprise_people');
                    $enterprise->type_cn = implode(',',$request->input('type_cn'));
                    $enterprise->enterprise_Introduction = $request->input('enterprise_Introduction');
                    $enterprise->filename = $request->input('filename');
                        $uctype = config('userconfig.type_cn');
                        $str = '';
                        foreach ($request->input('type_cn') as $v){
                            $k = array_search($v,$uctype);
                            $str .= $k.',';
                        }
                        $enterprise->type_int = rtrim($str,',');
                    $Personal = Users::where('username',$request->input('enterprise_people'))->first();
                    if($Personal){
                        if ($Personal->certification_type == 2 && $Personal->certification_shenhe == 3){
                            if($enterprise->save()){
                                Users::where('id',$enterprise->uid)->update(['certification_shenhe'=>8,'certification_type'=>2,'certification_status'=>2]);
                                return json_encode(['errcode'=>'1','errmsg'=>'保存成功'],JSON_UNESCAPED_UNICODE );
                            }
                            return json_encode(['errcode'=>'201','errmsg'=>'保存失败'],JSON_UNESCAPED_UNICODE );
                        }else{
                            return json_encode(['errcode'=>'4003','errmsg'=>'此代理人账号未认证'],JSON_UNESCAPED_UNICODE );
                        }
                    }else{
                        return json_encode(['errcode'=>'4003','errmsg'=>'未找到此代理人账号'],JSON_UNESCAPED_UNICODE );
                    }
                    }catch (ValidationException $validationException){
                        $messages = $validationException->validator->getMessageBag()->first();
                        return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
                    }

                }elseif($user->certification_type == 3){
                    return json_encode(['errcode'=>'4003','errmsg'=>'已程序员认证'],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未审核/未通过'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }

    //我的工作
    public function mywork(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                try {
                    $rules = [
                        'type'=>'required',
                    ];
                    //自定义消息
                    $messages = [
                        'type.required' => '栏目类型不为空',
                    ];
                    $this->validate($request, $rules, $messages);
                    //type 1:工作设置 2:推送项目 3：雇佣项目 4：承接项目
                    $type = $request->input('type');
                    if($type == 1){
                      if($user->certification_type == 3 ) {
                            $is_project = $user->is_project;
                            $project_cn = explode(',',$user->project_cn);
                            $programmer = Programmer::where('uid',$user->id)->first();
                            $starttime = $programmer->starttime;
                            $endtime = $programmer->endtime;
                            $starthour = $programmer->starthour;
                            $endhour = $programmer->endhour;
                            $data = [];
                            $data['is_project'] = $is_project;
                            $data['project_cn'] = $project_cn;
                            $data['starttime'] = $starttime;
                            $data['endtime'] = $endtime;
                            $data['starthour'] = $starthour;
                            $data['endhour'] = $endhour;
                            return json_encode(['errcode'=>'1','data'=>$data,'errmsg'=>'ok'],JSON_UNESCAPED_UNICODE );

                      }elseif ( $user->certification_type == 4){
                          $is_project = $user->is_project;
                          $project_cn = explode(',',$user->project_cn);
                          $data = [];
                          $data['is_project'] = $is_project;
                          $data['project_cn'] = $project_cn;
                          return json_encode(['errcode'=>'1','data'=>$data,'errmsg'=>'ok'],JSON_UNESCAPED_UNICODE );

                      }else{
                          return json_encode(['errcode'=>'4003','errmsg'=>'未签约认证'],JSON_UNESCAPED_UNICODE );
                      }
                    }elseif ($type == 2){
                        if($user->certification_type == 3 || $user->certification_type == 4){



                        }else{
                            return json_encode(['errcode'=>'4003','errmsg'=>'未签约认证'],JSON_UNESCAPED_UNICODE );
                        }
                    }elseif ($type == 3){
                        if($user->certification_type == 3 || $user->certification_type == 4){


                        }else{
                            return json_encode(['errcode'=>'4003','errmsg'=>'未签约认证'],JSON_UNESCAPED_UNICODE );
                        }
                    }elseif ($type == 4){
                        if($user->certification_type == 3 || $user->certification_type == 4){


                        }else{
                            return json_encode(['errcode'=>'4003','errmsg'=>'未签约认证'],JSON_UNESCAPED_UNICODE );
                        }
                    }else{
                        return json_encode(['errcode'=>'4002','errmsg'=>'没有该类型'],JSON_UNESCAPED_UNICODE );
                    }
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


    //我的主页
    public function myhomepage(Request $request){
        $user = \Auth::user();
        if($user){
            if($user->certification_status == 2){
                if($user->certification_type == 3 ) {
                    $programmer = Programmer::where('uid',$user->id)->first();
                    $starttime = $programmer->starttime;
                    $endtime = $programmer->endtime;
                    $starthour = $programmer->starthour;
                    $endhour = $programmer->endhour;
                    $data = [];
                    $data['qualifications'] = $programmer->qualifications;
                    $data['skills'] = $programmer->skills;
                    $data['experience'] = $programmer->experience;
                    $data['workstatus'] = $programmer->workstatus;
                    $data['dayamount'] = $programmer->dayamount;
                    $data['monthamount'] = $programmer->monthamount;
                    $data['personal_profile'] = $programmer->personal_profile;
                    $data['starttime'] = $starttime;
                    $data['endtime'] = $endtime;
                    $data['starthour'] = $starthour;
                    $data['endhour'] = $endhour;
                    return json_encode(['errcode'=>'1','data'=>$data,'errmsg'=>'ok'],JSON_UNESCAPED_UNICODE );
                }elseif ( $user->certification_type == 4){
                     $entserprise = Enterprise::where('uid',$user->id)->first();
                     $data = [];
                     $data['is_project'] = $user->is_project;
                     $data['enterprise_name'] = $entserprise->enterprise_name;
                     $data['enterprise_homepage'] = $entserprise->enterprise_homepage;
                     $data['enterprise_address'] = $entserprise->enterprise_address;
                     $data['enterprise_people'] = $entserprise->enterprise_people;
                     $data['enterprise_Introduction'] = $entserprise->enterprise_Introduction;
                    return json_encode(['errcode'=>'1','data'=>$data,'errmsg'=>'ok'],JSON_UNESCAPED_UNICODE );
                }else{
                    return json_encode(['errcode'=>'4003','errmsg'=>'未签约认证'],JSON_UNESCAPED_UNICODE );
                }
            }else{
                return json_encode(['errcode'=>'4003','errmsg'=>'未认证'],JSON_UNESCAPED_UNICODE );
            }
        }else{
            return json_encode(['errcode'=>'402','errmsg'=>'token已过期请替换'],JSON_UNESCAPED_UNICODE );
        }
    }


}
