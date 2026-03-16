<?php

namespace App\Api\Portal;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController {
    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;

    public function __construct() {
        $request       = Request();
        $mall_id       = $request->get('mall_id', '1');
        $this->config  = config('wechat.min' . $mall_id);
        $this->mall_id = $mall_id;
    }
    // 小程序登陆 获取openid 返回是否需要收集用户头像和昵称
    public function wxlogin(Request $request) {
        /*$request->validate(
            [
                //'wx_code'   => 'required',
                //'nickname'  => 'required',
                //'avatarUrl' => 'required',
                //'openid'    => 'required',
            ], [
                'wx_code.required' => '微信code不能为空',
                //'mall_id.required'   => '小程序mallid不能为空',
                'nickname.required'  => '用户昵称不能为空',
                'avatarUrl.required' => '用户头像不能熔',
                'openid.required'    => '微信openid不能为空',
            ]
        );*/
        $wx_code    = $request->get('wx_code', '');
        $wx_openid    = $request->get('wx_openid', '');
        if(empty($wx_code) && empty($wx_openid)){
            return returnData(500, 0, [], 'wx_code 或者 wx_openid 必须传一个.');
        }


        if(!empty($wx_code) && empty($wx_openid)){
            $app     = Factory::miniProgram($this->config);
            $infos   = $app->auth->session($wx_code);
            // 微信登陆失败
            if (!empty($infos['errcode'])) {
                return returnData(50001,0,[],$infos['errmsg']);
            }
            $wx_openid = $infos['openid'];
        }



        $resdata = [];
        if (!empty($wx_openid)) {
            $infos['not_user_avatar'] = 0;
            $userinfo                 = \App\User::where(['openid' => $wx_openid])->first();
            if (!empty($userinfo->id)) {
                $infos['not_user_avatar'] = 1;
                $token = JWTAuth::fromUser($userinfo);
                return returnData(200, 1, ['token' => $token,'openid'=>$wx_openid], 'ok');
                //$infos['userinfo']        = $userinfo;
            }
            //$resdata = $infos;

        }
        $nickname  = $request->nickname;
        $avatarUrl = $request->avatarUrl;
        $openid    = $wx_openid;
        // 注册用户
        $user = (new \App\Services\UserService())->createToOpenidUser($openid, $nickname, $avatarUrl);
        if (!empty($user->id)) {
            $userinfo                 = \App\User::where(['openid' => $wx_openid])->first();
            $token = JWTAuth::fromUser($userinfo);
            return returnData(200, 1, ['token' => $token,'openid'=> $openid ], 'ok');
        }

        /*
         // 账密获取token
         $input = ['email'=> '3664839@qq.com','password'=>'123456abc'];//$request->only('email', 'password');
         $jwt_token = null;
         if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);*/
        return returnData(205, 0, [], '用户Code无效');
    }
    // 获取用户资料
    public function getUserinfo(Request $request){
        $this->user = JWTAuth::parseToken()->authenticate();
        return returnData(200, 1, ['userinfo' => $this->user], 'ok');
    }


    // 获取用户订单
    public function getUserOrder(Request $request){


    }

    // 用户注册并登陆
    public function wxUserLoginOrRegiser(Request $request) {
        $request->validate(
            [
                //'mall_id'   => 'required',
                'nickname'  => 'required',
                'avatarUrl' => 'required',
                'openid'    => 'required',
            ], [
                'mall_id.required'   => '小程序mallid不能为空',
                'nickname.required'  => '用户昵称不能为空',
                'avatarUrl.required' => '用户头像不能熔',
                'openid.required'    => '微信openid不能为空',
            ]
        );
        $nickname  = $request->nickname;
        $avatarUrl = $request->avatarUrl;
        $openid    = $request->openid;
        // 注册用户
        $user = (new \App\Services\UserService())->createToOpenidUser($openid, $nickname, $avatarUrl);
        if (!empty($user->id)) {
            return returnData(200, 1, ['userinfo' => collect($user)->toArray()], 'ok');
        }
        return returnData(205, 0, [], '用户注册失败');
    }
}
