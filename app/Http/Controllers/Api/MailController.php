<?php

namespace App\Http\Controllers\Api;

use App\Models\Smslog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class MailController extends Controller
{
    //
    //发送邮箱验证码
    public function emailcode(Request $request){
        try {
            //规则
            $rules = [
                'email'=>'required|email',
            ];
            //自定义消息
            $messages = [
                'email.required' => '请输入邮箱',
                'email.email' => '邮箱不合法',
            ];

            $this->validate($request, $rules, $messages);
            $email = $request->input('email');
            $smslog = (new Smslog())->sendEmailVerifyCode($email);
            return $smslog;

        }catch (ValidationException $validationException){
            $messages = $validationException->validator->getMessageBag()->first();
            return json_encode(['errcode'=>'1001','errmsg'=>$messages],JSON_UNESCAPED_UNICODE );
        }
    }
}
