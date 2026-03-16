<?php

namespace App\Services;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Hotel\UserLevel;
use App\Models\Hotel\UserRongbaopayOpenid;
/**
 * 用户类
 * @package App\Services
 * anthor Fox
 */
class UserService extends BaseService {
    protected $pay_type_arr = [
        'wx'     => 'wx_openid',
        'alipay' => 'alipay_openid',
        'bank'   => 'bankpay_openid',
    ];

    public function createToPhoneUser($phone,$password_str = '',$username_str = ''){
        // 注册或登陆
        if(!empty($password_str)){
            $password = $password_str;
        }else{
            $password = $phone.'abc';
        }
        if(!empty($username_str)){
            $username = $username_str;
        }else{
            $username = 'user'.$phone;
        }
        $image_url = env('APP_URL').'/img/toux1.png';
        $email = $phone.'@189.com';
        $api_token = Str::random(64);
        $insdata = [
            'name'      => $username,
            'email'     => $email,
            'password'  => Hash::make($password),
            'api_token' => $api_token,
            'avatar' => $image_url,
            'phone' => $phone
        ];
        $user     = User::firstOrCreate(['phone' => $phone],$insdata);
        return $user;
    }

    public function createToBmnameUser($bmname,$bm_phone = '',$seller_id = ''){
        // 注册或登陆
        $password = '123456abc';
        $username = $bmname;
        if(!empty($bm_phone)){
            $phone = $bm_phone;
        }else{
            $phone = '18900001000';
        }

        $avatar = env('APP_URL').'/img/toux1.png';
        $email = $phone.'@189.com';
        $api_token = Str::random(64);
        //$level_id = UserLevel::where(['hotel_id'=> $hotel_id,'level_num'=>1])->value('id');
        $insdata = [
            'name'      => $username,
            'email'     => $email,
            'password'  => Hash::make($password),
            'api_token' => $api_token,
            'avatar' => $avatar,
            'phone' => $phone
        ];
        // ['name' => $username],
        $user     = User::create($insdata);
        if(!empty($user->id) && !empty($seller_id)){
            //
            // UsersSeller::create([
            //     'user_id' => $user->id,
            //     'seller_id' => $seller_id,
            // ]);
        }
        return $user;
    }

    // 微信用户自动注册
    public function createToOpenidUser($openid,$nickname = '',$wxavatar = '',$seller_id = ''){
        $hotel_id = \Request::get('hotel_id');
        $pid = \Request::get('pid');
        // 查看用户是否已经注册
        $userinfo = User::where(['openid'=> $openid,'hotel_id'=>$hotel_id])->first();
        if(!empty($userinfo->id)){
            $this->linkUserParent($userinfo,$pid);
            return $userinfo;
        }
        // 注册或登陆
        $password = '123456abc';
        //$username = $nickname;
        $phone = null;
        if(!empty($wxavatar)){
            $avatar = $wxavatar;
        }else{
            $avatar = env('APP_URL').'/img/toux1.png';
        }
        $email = Str::random(8).'@199.com';
        $api_token = Str::random(32);

        $level_id = UserLevel::where(['hotel_id'=> $hotel_id,'level_num'=>1])->value('id');
        if(empty($level_id)){
            $level_id  = 0;
        }
        $insdata = [
            'hotel_id' => $hotel_id,
            'name'      => 'null',
            'email'     => $email,
            'password'  => Hash::make($password),
            'api_token' => $api_token,
            'avatar' => $avatar,
            'phone' => $phone,
            'openid' => $openid,
            'level_id' => $level_id,// 普通用户
            'user_source' => 'wx_min',
        ];
        if(!empty($nickname)){
            $insdata['name'] = $nickname;
            $insdata['nick_name'] = $nickname;
        }
        $user     = User::firstOrCreate(['openid'=> $openid,'hotel_id' => $hotel_id],$insdata);
        if(!empty($user->id)){
            // 更新用户名
            if($user->name == 'null'){
                $models = User::find($user->id);
                $models->name = '微信用户';
                $models->nick_name = '微信用户';
                $models->save();
            }
            $this->linkUserParent($user,$pid);
        }
        return $user;
    }

    // 关联上级
    public function linkUserParent($userinfo,$pid){
        if(!empty($pid) && empty($userinfo->temp_parent_id)){
            // 查找父级是否存在
            $pid_user = User::where(['id'=> $pid,'hotel_id' => $userinfo->hotel_id])->first();
            if(!empty($pid_user->id)){
                $updata['temp_parent_id'] = $pid;
                $updata['junior_at'] = date('Y-m-d H:i:s');
                User::where(['id'=> $userinfo->id])->update($updata);

                // 直接发放 邀请好友优惠券
                $where = [];
                $where[] = ['hotel_id','=', $userinfo->hotel_id];
                $where[] = ['status' ,'=', 1];
                $where[] = ['type' ,'=', 3];
                $coupon_info = \App\Models\Hotel\Coupon::where($where)->first();
                if($coupon_info){
                    $insdata = [
                        'user_id'       => $userinfo->id,
                        'hotel_id'      => $userinfo->hotel_id,
                        'coupon_id'     => $coupon_info->id,
                        'expire_time' => $coupon_info->end_time,
                        'coupon_status' => 0,
                    ];
                    \App\Models\Hotel\Usercoupon::receive($insdata);
                }
            }
        }
    }

    // 更新位置信息
    public function upLocationInfo($user_id,$data){
        $status = User::where(['id'=>$user_id])->update($data);
        return $status;
    }


    // 检查微信openid是否已经注册
    public static function checkUserRegister($wx_code,$hotel_id,$is_register = false){
        $miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
        $infos       = $miniProgram->auth->session($wx_code);
        // 微信登陆失败
        if (!empty($infos['errcode'])) {
            return false;
        }
        $wx_openid = $infos['openid'];

        $userinfo                 = \App\User::where(['openid' => $wx_openid, 'hotel_id' => $hotel_id])->first();
        if(!$userinfo){
            if($is_register){
                $user = (new UserService())->createToOpenidUser($wx_openid, '微信用户', 'https://hotel.rongbaokeji.com/img/toux1.png');
                if(!empty($user->id)){
                    return $user->id;
                }
            }
            return false;
        }
        return $userinfo->id;
    }

    // 验证 绑定融宝支付的openid
    public function verifyRongbaopayOpenid($hotel_id,$user_id, $pay_type, $openid) {
        if (empty($this->pay_type_arr[$pay_type])) {
            return '没有此交易渠道类型';
        }
        // 第一种 这个openid 在这个酒店，是否已经被他人绑定
        $field = $this->pay_type_arr[$pay_type];
        $info  = UserRongbaopayOpenid::where(['hotel_id' => $hotel_id,$field => $openid])->first();
        if(!empty($info->id) && $info->user_id != $user_id){
            return '此订单已被他人绑定,无权限查看';
        }

        $this->saveRongbaopayOpenid($hotel_id, $pay_type, $openid,$user_id); // user 绑定融宝支付的openid
        return true;

        //return UserRongbaopayOpenid::where(['user_id' => $this->id, 'hotel_id' => $hotel_id, $field => $openid])->count();
    }

    /**
     * @desc 绑定融宝支付的openid
     * @param $hotel_id 酒店ID
     * @param $pay_type 支付渠道类型
     * @param $openid 支付用户openid
     * @param bool $up_force 是否强制更新
     * @return bool
     * author eRic
     * dateTime 2024-05-16 10:29
     */
    public function saveRongbaopayOpenid($hotel_id, $pay_type, $openid,$user_id,$up_force = false) {
        if (empty($this->pay_type_arr[$pay_type])) {
            return false;
        }
        $field = $this->pay_type_arr[$pay_type];
        $minapp_wx_code = \App\User::where(['id'=> $user_id])->value('openid');

        // 以user_id 和酒店ID查询
        $info  = UserRongbaopayOpenid::where(['user_id' => $user_id, 'hotel_id' => $hotel_id])->first();
        if (!empty($info->minapp_wx_code)) {
            if (!empty($info->$field)) {
                return false;
            }
            $updata = [
                $field => $openid,
            ];
            return UserRongbaopayOpenid::where(['user_id' => $user_id, 'hotel_id' => $hotel_id])->update($updata);
        }

        $insdata = [
            'user_id'  => $user_id,
            'minapp_wx_code' => $minapp_wx_code,
            'hotel_id' => $hotel_id,
            $field     => $openid,
        ];
        return UserRongbaopayOpenid::create($insdata);
    }

    // 通过 wx_code 获取小程序微信openid
    public static function wxcodeGetUserinfo($wx_code,$hotel_id){
        $miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
        $infos       = $miniProgram->auth->session($wx_code);
        // 微信登陆失败
        if (!empty($infos['errcode'])) {
            $errormsg = !empty($res['errmsg']) ? $res['errmsg'] : '获取微信用户openid失败';
            return false;
        }
        $wx_openid    = $infos['openid'];
        // 用户是否注册
        $userinfo = \App\User::where(['openid' => $wx_openid, 'hotel_id' => $hotel_id])->first();
        if (!$userinfo) {
            $user = (new UserService())->createToOpenidUser($wx_openid, '微信用户', 'https://hotel.rongbaokeji.com/img/toux1.png');
            if (!empty($user->id)) {
                return $user;
            }
        }
        return $userinfo;
    }
}