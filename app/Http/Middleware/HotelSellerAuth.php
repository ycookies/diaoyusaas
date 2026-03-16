<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Str;

// 酒店 授权接口中间件
class HotelSellerAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {

        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return response ()->json ([
                'code' => 402,
                'data' => [],
                'msg' => '缺少token',
            ], 402);
        }

        try {
            $admin = auth::guard('sellerapi')->authenticate($token);
        } catch (TokenExpiredException $e) {
            return response ()->json ([
                'code'  => 402,
                'msg'   => 'token过期',
                'data'  => [],
            ], 402);
        } catch (JWTException $e) {
            return response ()->json ([
                'code'  => 402,
                'msg'   => 'token无效',
                'data'  => [],
            ], 402);
        } catch (\Exception $e) {
            return response ()->json ([
                'code'  => 402,
                'msg'   => 'token无效',
                'data'  => [],
            ], 402);
        }

        if (!$admin) {
            return response ()->json ([
                'code'  => 402,
                'msg'   => '用户不存在',
                'data'  => [],
            ], 402);
        }

        return $next($request);
    }

    public function bearerToken($request){
        $header = $request->header('Authorization', '');

        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        return '';
    }
}
