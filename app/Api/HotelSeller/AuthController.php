<?php

namespace App\Api\HotelSeller;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;
use App\Models\MerchantUser;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController {

    /**
     * 构造函数
     */
    public function __construct(Request $request)
    {
        // 这里额外注意了：官方文档样例中只除外了『login』
        // 这样的结果是，token 只能在有效期以内进行刷新，过期无法刷新
        // 如果把 refresh 也放进去，token 即使过期但仍在刷新期以内也可刷新
        // 不过刷新一次作废
        $this->middleware('HotelSellerAuth', ['except' => ['login','retpassword']]);
        // 另外关于上面的中间件，官方文档写的是『auth:api』
        // 但是我推荐用 『jwt.auth』，效果是一样的，但是有更加丰富的报错信息返回

        $actions    = $request->route()->getAction();
        $actionName = isset($actions['controller']) ? $actions['controller'] : '';

        if($actionName && stripos($actionName, '@')){
            $actionName = strtolower(explode('@', $actionName)[1]);
        }

        if($actionName != 'login'){
            parent::__construct();
        }
    }

    // 商家登陆获取token
    public function login(Request $request){
        $request->validate(
            [
                'phone'  => 'required',
                'password' => 'required',
            ], [
                'phone.required'   => '手机号码 不能为空',
                'password.required'  => '登陆密码 不能为空',
            ]
        );
        $phone = $request->get('phone');
        $password = $request->get('password');
        $center = MerchantUser::where(['phone'=>$phone])->first(); // 'is_active'=>1

        if(empty($center)){
            return response()->json([
                'code'  => 500,
                'msg'   => '账户信息不存在'
            ]);
        }
        if($center->is_active != 1){
            return response()->json([
                'code'  => 500,
                'msg'   => '该账户未激活'
            ]);
        }
        /*if($center->type != 1){
            return response()->json([
                'code'  => 500,
                'msg'   => '该账户未开通后台权限'
            ]);
        }*/

        if(!Hash::check($password, $center->password)){
            return [
                'code'  => 500,
                'msg'   => '账户密码不正确'
            ];
        }

        if (!$token = auth('sellerapi')->fromUser($center)) {
            return response()->json([
                'code'  => 500,
                'msg'   => '登录失败',
            ]);
        }

        $data = [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth('sellerapi')->factory()->getTTL() * 60 * 24 * 7,
            'username'      => $center->username
        ];
        return returnData(200, 1, $data, '登陆成功');
    }

    // 收银员登陆获取token
    public function cashierLogin(){

    }

    // 找回密码
    public function retpassword(Request $request){
        $request->validate(
            [
                'phone'  => 'required',
                'phone_code'  => 'required',
                'password' => 'required',
                'confirm_password' => 'required',
            ], [
                'phone.required'   => '手机号码 不能为空',
                'phone_code.required'   => '手机验证码 不能为空',
                'password.required'  => '登陆密码 不能为空',
                'confirm_password.required'  => '确认密码 不能为空',
            ]
        );
        $phone = $request->get('phone');
        $phone_code = $request->get('phone_code');
        $password = $request->get('password');
        $confirm_password = $request->get('confirm_password');
        $seller = MerchantUser::where(['phone'=>$phone])->first(); // 'is_active'=>1

        if(empty($seller)){
            return response()->json([
                'code'  => 500,
                'msg'   => '账户信息不存在'
            ]);
        }
        if($seller->is_active != 1){
            return response()->json([
                'code'  => 500,
                'msg'   => '该账户未激活'
            ]);
        }
        // 验证手机号码
        $serv = new \App\Services\SmsService();
        $ste =  $serv->isCodeCorrect($phone,$phone_code);
        if(!$ste){
            return returnData(204,0,[],'验证码不正确或已过期');
        }

        $seller->password = Hash::make($password);

        //$seller->setRememberToken(Str::random(60));

        $seller->save();
        return returnData(200,1,[],'重置成功');
    }

    /**
     * 用户信息
     */
    public function me(){
        $seller = auth('sellerapi')->user();
        return response()->json([
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $seller
        ]);
    }

    /**
     * 退出
     */
    public function logout(){
        auth('sellerapi')->logout();

        return response()->json([
            'code'  => 200,
            'msg'   => '退出成功'
        ]);
    }

    /**
     * 刷新token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('sellerapi')->refresh());
    }

    protected function respondWithToken($token){
        return response()->json([
            'code'          => 200,
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth('sellerapi')->factory()->getTTL() * 60 * 24 * 30
        ]);
    }


}
