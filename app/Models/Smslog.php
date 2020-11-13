<?php

namespace App\Models;

use App\Service\Sms\Sms;
use http\Env\Request;
use Illuminate\Support\Facades\Cache;
use Mail;
use Illuminate\Database\Eloquent\Model;


class Smslog extends Model
{
    //
    protected $table = 'smslog';
    public $timestamps = 'flase';
    protected $fillable =['id','mobile','session_id','code','status','scene','addtime','error_msg'];

    //发送手机验证码
    public function sendMobileVerifyCode($mobile,$n){
        $scene = $this->Scene($n);//场景0：验证码。1：短信通知。2：推广短信。3：国际/港澳台消息。
        $smsTemplate=$this->getSmsTemplateByScene($scene);//去下边找这个方法！自己的数据库中存在着从阿里云申请的短信模板
        $session_id=md5(time().mt_rand(1,999999999));//没啥用的
        $code = rand(100000, 999999);
        $param=array('mobile'=>$mobile,'sms_sign'=>$smsTemplate['templatename'],'sms_tpl_code'=>$smsTemplate['templatecode'],'code'=>$code);
       $response = Sms::send($param);
        $data=[
            'mobile'=>$mobile,
            'session_id'=>$session_id,
            'code'=>$code,
            'status'=>1,
            'scene'=>0,
            'addtime'=>date('Y-m-d H:i:s'),
            'error_msg'=>$response['Message'] == 'OK' ? 'ok' : $response['Message'],
        ];
        $a = $this->addSmsLog($data);//记录发短信记录表
        if('OK' == $response['Code']){

            return Response()->json( msg(1,['code'=>$code] , '验证码发送成功'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
        return Response()->json(msg(-2,[] , '验证码发送失败'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function Scene($sc){
        return $sc;
    }

    public function getSmsTemplateByScene($scene){
        return SmsTemplate::where(['templatetype' => $scene])->first();//把阿里云的短信模板放到了数据库中了
    }

    public function addSmsLog($param){

        try{
            $result = Smslog::create($param);
            if(!isset($result->id)){
                // 验证失败 输出错误信息
                return Response()->json(msg(-1, '', $this->getError()))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            }else{
                return Response()->json(msg(1, $result, '保存成功'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            }
        }catch (\Exception $e){
            return Response()->json(msg(-2, '', $e->getMessage()))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    //发送邮箱验证码
    public function sendEmailVerifyCode($email){
        $model = Smslog::where('mobile',$email)->orderBy('addtime','desc')->limit(1)->first();
        $session_id=md5(time().mt_rand(1,999999999));//没啥用的
        $code = rand(100000, 999999);
        if (empty($model)){
            //为空
            $code = rand(100000, 999999);
            Mail::send('email',['name'=>$email,'code'=>$code,'time'=>date('Y-m-d')],function ($message) use($email){
               $message->to($email);
               $message->subject('发包狗邮箱验证');
            });
            if(count(Mail::failures()) < 1){
                self::cacheCode($email,$code);
                $data=[
                    'mobile'=>$email,
                    'session_id'=>$session_id,
                    'code'=>$code,
                    'status'=>1,
                    'scene'=>0,
                    'addtime'=>date('Y-m-d H:i:s'),
                    'error_msg'=> 'ok',
                ];
                $a = $this->addSmsLog($data);//记录发短信记录表
                return Response()->json( msg(1,['code'=>$code] , '验证码发送成功'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            }else{
                return Response()->json(msg(-2,[] , '验证码发送失败'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            }
        }else{
            //不为空
            $codetime = floor((time() - strtotime($model->addtime))/60);
            $time = 5;
            if($codetime > $time){
                $code = rand(100000, 999999);
                Mail::send('email',['name'=>$email,'code'=>$code,'time'=>date('Y-m-d')],function ($message) use($email){
                    $message->to($email);
                    $message->subject('发包狗邮箱验证');
                });
                if(count(Mail::failures()) < 1){
                    self::cacheCode($email,$code);
                    $data=[
                        'mobile'=>$email,
                        'session_id'=>$session_id,
                        'code'=>$code,
                        'status'=>1,
                        'scene'=>0,
                        'addtime'=>date('Y-m-d H:i:s'),
                        'error_msg'=> 'ok',
                    ];
                    $a = $this->addSmsLog($data);//记录发短信记录表
                    return Response()->json( msg(1,['code'=>$code] , '验证码发送成功'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
                }else{
                    return Response()->json(msg(-2,[] , '验证码发送失败'))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
                }
            }else{
                return  Response()->json(msg(-403,[] , "发送过于频繁，请" . $time . "分钟后再试"))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            }

        }




    }

    protected static function cacheCode($email,$code){
        $key =  $email;
        Cache::put($key,$code,5);
        return true;
    }

}
