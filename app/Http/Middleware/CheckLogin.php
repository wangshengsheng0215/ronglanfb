<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckLogin  extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 检查此次请求中是否带有 token，如果没有则抛出异常。
        $this->checkForToken($request);

        // 使用 try 包裹，以捕捉 token 过期所抛出的 TokenExpiredException  异常
        try {
            // 检测用户的登录状态，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
            throw new UnauthorizedHttpException('jwt-auth', '未登录');
            //return response()->json(['code'=>402,'data'=>[],'msg'=>'未登录']);
        }catch (TokenBlacklistedException $exception){
            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        } catch (TokenExpiredException $exception) {
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
            try {

                // 刷新用户的 token
                $token = $this->auth->refresh();
                $next($request)->headers->set('Access-Control-Allow-Headers','Authorization');
                $next($request)->headers->set('Authorization','Bearer '.$token); // 给当前的请求设置性的token,以备在本次请求中需要调用用户信息


                // 使用一次性登录以保证此次请求的成功
                Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
                // return response()->json(['code'=>402,'data'=>[],'msg'=>$exception->getMessage()]);
                //return json_encode(['errcode'=>'402','errmsg'=>'账号信息过期请重新登录'],JSON_UNESCAPED_UNICODE );
            }
        }
        // 在响应头中返回新的 token
        $next($request)->headers->set('Access-Control-Allow-Headers','Authorization');
        return $this->setAuthenticationHeader($next($request), $token);
    }
}

