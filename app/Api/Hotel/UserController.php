<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\HongbaoUser;
use App\Models\Hotel\MemberVipSet;
use App\Models\Hotel\OftenLvkeinfo;
use App\Models\Hotel\Usercoupon;
use App\Models\Hotel\UserLevel;
use App\Models\Hotel\UserMember;
use App\Models\Hotel\WxappConfig;
use App\Models\Hotel\WxCardTpl;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController {
    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    public function __construct() {
        $request      = Request();
        $hotel_id     = $request->get('hotel_id');
        $this->config = WxappConfig::getConfig($hotel_id);
        //$this->config  = config('wechat.min' . $mall_id);

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
        info($request->all());
        $wx_code   = $request->get('wx_code', '');
        $wx_openid = $request->get('wx_openid', '');
        $hotel_id  = $request->get('hotel_id', '');
        $app_id    = $request->get('app_id', '');
        $extraData = $request->get('extraData');
        $pid       = $request->get('pid');
        if (empty($wx_code) && empty($wx_openid)) {
            return returnData(500, 0, [], 'wx_code 或者 wx_openid 必须传一个.');
        }

        if (!empty($wx_code) && empty($wx_openid)) {
            //$app     = Factory::miniProgram($this->config);
            //$infos   = $app->auth->session($wx_code);
            $miniProgram = app('wechat.open')->miniProgram($app_id);
            $infos       = $miniProgram->auth->session($wx_code);
            // 微信登陆失败
            if (!empty($infos['errcode'])) {
                return returnData(50001, 0, [], $infos['errmsg']);
            }
            $wx_openid = $infos['openid'];
        }
        info([$wx_code,$wx_openid]);


        $resdata = [];
        if (!empty($wx_openid)) {
            $infos['not_user_avatar'] = 0;
            $userinfo                 = \App\User::where(['openid' => $wx_openid, 'hotel_id' => $hotel_id])->first();
            if (!empty($userinfo->id)) {

                $this->addUserCard($userinfo,$extraData,$hotel_id);

                $infos['not_user_avatar'] = 1;
                $token                    = JWTAuth::fromUser($userinfo);
                return returnData(200, 1, ['token' => $token, 'user_id' => $userinfo->id, 'openid' => $wx_openid], 'ok');
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
            $userinfo = \App\User::where(['openid' => $wx_openid, 'hotel_id' => $hotel_id])->first();

            // 获取小程序开卡信息
            $this->addUserCard($userinfo,$extraData,$hotel_id);

            $token = JWTAuth::fromUser($userinfo);
            return returnData(200, 1, ['token' => $token, 'user_id' => $userinfo->id, 'openid' => $openid], 'ok');
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



    // 增加用户会员卡
    public function addUserCard($userinfo,$extraData,$hotel_id){

        // 获取小程序开卡信息
        if (!empty($extraData) && ($userinfo->card_code == '' || $userinfo->card_code == null)) {
            $gzhobj        = app('wechat.open')->hotelWxgzh($hotel_id);
            $extraData_arr = json_decode($extraData, true);
            info('把开卡数据数组化');
            info($extraData_arr);

            // 激活会员卡
            if (!empty($extraData_arr['code'])) {
                $info = [
                    'card_id'           => $extraData_arr['card_id'],
                    'membership_number' => $extraData_arr['code'], //会员卡编号，由开发者填入，作为序列号显示在用户的卡包里。可与Code码保持等值。
                    'code'              => $extraData_arr['code'], //创建会员卡时获取的初始code。
                    'init_bonus'        => 0, //初始积分，不填为0。
                    'init_balance'      => 0, //初始余额，不填为0。
                ];

                $res2 = $gzhobj->card->member_card->activate($info);
                addlogs('member_card_activate', $info, $res2);
                if (isset($res2['errcode']) && $res2['errcode'] != 0) {
                    info('激活会员卡失败:' . json_encode($info, JSON_UNESCAPED_UNICODE));
                }
            }

            // 获取开卡资料信息
            if (!empty($extraData_arr['activate_ticket'])) {
                //$level_id = UserLevel::where(['hotel_id' => $hotel_id, 'level_num' => 1])->value('id');
                $user_upinfo = [
                    'card_code' => $extraData_arr['code'],
                    'level_id' => 1, // 普卡会员
                ];
                if(strpos($userinfo->nick_name,'微信用户') !== false){
                    $user_upinfo['name'] = '普卡会员';
                    $user_upinfo['nick_name'] = '普卡会员';
                }
                if (!empty($extraData_arr['wx_activate_after_submit_url'])) {
                    /**
                     * [card_id] => pRpZY6gh7htgVqlBqiazJIIUWdXI
                     * [encrypt_code] => DOQH58wxsH0b8jlIVZnsc4VSEDkAc92+L8v1dlm7zaA=
                     * [openid] => oRpZY6qR8tPqk_fQqks08YhcjBYY
                     * [outer_str] => minapp
                     */
                    $query = parse_url($extraData_arr['wx_activate_after_submit_url'], PHP_URL_QUERY); // 提取 URL 中的查询参数
                    parse_str($query, $params); // 将查询参数转换为数组
                    if (!empty($params['openid'])) {
                        $user_upinfo['gzh_openid'] = $params['openid']; // 公众号openid
                    }
                }
                // 获取开卡资料信息
                $res = $gzhobj->card->member_card->getActivationForm($extraData_arr['activate_ticket']);
                addlogs('member_card_getActivationForm', $extraData_arr, $res);
                if (!empty($res['info'])) {
                    $common_field_list = $res['info']['common_field_list'];
                    $custom_field_list = $res['info']['custom_field_list'];
                    // 开卡表单 必填项
                    foreach ($common_field_list as $key => $items) {
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_MOBILE') {
                            $user_upinfo['phone'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_NAME') {
                            $user_upinfo['zs_name'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_SEX') {
                            $user_upinfo['sex'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_IDCARD') {
                            $user_upinfo['idCard'] = $items['value'];
                        }
                    }
                    // 开卡表单 选填项
                    if (!empty($custom_field_list)) {
                        foreach ($custom_field_list as $key => $item2) {

                        }
                    }
                }

                // 更新会员信息
                \App\User::where(['id' => $userinfo->id])->update($user_upinfo);

                // 加入酒店会员记录表
                $insdata = [
                    'hotel_id' => $hotel_id,
                    'uid'      => $userinfo->id,
                    'phone'    => $user_upinfo['phone'],
                    'name'     => $user_upinfo['zs_name'],
                    'id_card'  => !empty($user_upinfo['idCard']) ? $user_upinfo['idCard'] : '',
                ];
                UserMember::addUser($insdata);

                // 添加常住旅客
                $where1  = ['user_id' => $userinfo->id, 'zname' => $user_upinfo['zs_name'], 'hotel_id' => $hotel_id];
                $insdata = [
                    'hotel_id'   => $hotel_id,
                    'user_id'    => $userinfo->id,
                    'zname'      => $user_upinfo['zs_name'],
                    'phone'      => $user_upinfo['phone'],
                    'is_benren'  => 1,
                    'is_default' => 1,
                ];
                if (!empty($user_upinfo['idCard'])) {
                    $insdata['idcard'] = $user_upinfo['idCard'];
                }
                OftenLvkeinfo::createOrUpdate($where1, $insdata);
                // 发放 新人 入会优惠券
                $where       = [];
                $where[]     = ['hotel_id', '=', $hotel_id];
                $where[]     = ['status', '=', 1];
                $where[]     = ['grant_type', '=', 4];
                $coupon_info = \App\Models\Hotel\Coupon::where($where)->first();
                if ($coupon_info) {
                    $insdata = [
                        'user_id'       => $userinfo->id,
                        'hotel_id'      => $hotel_id,
                        'coupon_id'     => $coupon_info->id,
                        'expire_time'   => $coupon_info->end_time,
                        'coupon_status' => 0,
                    ];
                    \App\Models\Hotel\Usercoupon::receive($insdata);
                }
            }
            return true;
        }
        return false;
    }


    // 备份一下代码;
    public function blackcode(){
        if (!empty($extraData) && ($userinfo->card_code == '' || $userinfo->card_code == null)) {
            $gzhobj        = app('wechat.open')->hotelWxgzh($hotel_id);
            $extraData_arr = json_decode($extraData, true);
            info('把开卡数据数组化');
            info($extraData_arr);

            // 激活会员卡
            if (!empty($extraData_arr['code'])) {
                $info = [
                    'card_id'           => $extraData_arr['card_id'],
                    'membership_number' => $extraData_arr['code'], //会员卡编号，由开发者填入，作为序列号显示在用户的卡包里。可与Code码保持等值。
                    'code'              => $extraData_arr['code'], //创建会员卡时获取的初始code。
                    'init_bonus'        => 0, //初始积分，不填为0。
                    'init_balance'      => 0, //初始余额，不填为0。
                ];

                $res2 = $gzhobj->card->member_card->activate($info);
                addlogs('member_card_activate', $info, $res2);
                if (isset($res2['errcode']) && $res2['errcode'] != 0) {
                    info('激活会员卡失败:' . json_encode($info, JSON_UNESCAPED_UNICODE));
                }
            }

            // 获取开卡资料信息
            if (!empty($extraData_arr['activate_ticket'])) {
                $user_upinfo = [
                    'card_code' => $extraData_arr['code']
                ];
                if (!empty($extraData_arr['wx_activate_after_submit_url'])) {
                    /**
                     * [card_id] => pRpZY6gh7htgVqlBqiazJIIUWdXI
                     * [encrypt_code] => DOQH58wxsH0b8jlIVZnsc4VSEDkAc92+L8v1dlm7zaA=
                     * [openid] => oRpZY6qR8tPqk_fQqks08YhcjBYY
                     * [outer_str] => minapp
                     */
                    $query = parse_url($extraData_arr['wx_activate_after_submit_url'], PHP_URL_QUERY); // 提取 URL 中的查询参数
                    parse_str($query, $params); // 将查询参数转换为数组
                    if (!empty($params['openid'])) {
                        $user_upinfo['gzh_openid'] = $params['openid']; // 公众号openid
                    }
                }
                // 获取开卡资料信息
                $res = $gzhobj->card->member_card->getActivationForm($extraData_arr['activate_ticket']);
                addlogs('member_card_getActivationForm', $extraData_arr, $res);
                if (!empty($res['info'])) {
                    $common_field_list = $res['info']['common_field_list'];
                    $custom_field_list = $res['info']['custom_field_list'];
                    // 开卡表单 必填项
                    foreach ($common_field_list as $key => $items) {
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_MOBILE') {
                            $user_upinfo['phone'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_NAME') {
                            $user_upinfo['zs_name'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_SEX') {
                            $user_upinfo['sex'] = $items['value'];
                        }
                        if ($items['name'] == 'USER_FORM_INFO_FLAG_IDCARD') {
                            $user_upinfo['idCard'] = $items['value'];
                        }
                    }
                    // 开卡表单 选填项
                    if (!empty($custom_field_list)) {
                        foreach ($custom_field_list as $key => $item2) {

                        }
                    }
                }

                // 更新会员信息
                \App\User::where(['id' => $user->id])->update($user_upinfo);

                // 加入酒店会员记录表
                $insdata = [
                    'hotel_id' => $hotel_id,
                    'uid'      => $user->id,
                    'phone'    => $user_upinfo['phone'],
                    'name'     => $user_upinfo['zs_name'],
                    'id_card'  => !empty($user_upinfo['idCard']) ? $user_upinfo['idCard'] : '',
                ];
                UserMember::addUser($insdata);

                // 添加常住旅客
                $where1  = ['user_id' => $user->id, 'zname' => $user_upinfo['zs_name'], 'hotel_id' => $hotel_id];
                $insdata = [
                    'hotel_id'   => $hotel_id,
                    'user_id'    => $user->id,
                    'zname'      => $user_upinfo['zs_name'],
                    'phone'      => $user_upinfo['phone'],
                    'is_benren'  => 1,
                    'is_default' => 1,
                ];
                if (!empty($user_upinfo['idCard'])) {
                    $insdata['idcard'] = $user_upinfo['idCard'];
                }
                OftenLvkeinfo::createOrUpdate($where1, $insdata);
                // 发放 新人 入会优惠券
                $where       = [];
                $where[]     = ['hotel_id', '=', $hotel_id];
                $where[]     = ['status', '=', 1];
                $where[]     = ['type', '=', 4];
                $coupon_info = \App\Models\Hotel\Coupon::where($where)->first();
                if ($coupon_info) {
                    $insdata = [
                        'user_id'       => $user->id,
                        'hotel_id'      => $hotel_id,
                        'coupon_id'     => $coupon_info->id,
                        'expire_time'   => $coupon_info->end_time,
                        'coupon_status' => 0,
                    ];
                    \App\Models\Hotel\Usercoupon::receive($insdata);
                }
            }

        }
    }

    // 获取用户资料
    public function getUserinfo(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $hotel_id   = $request->get('hotel_id');
        $userinfo   = $this->user;

        // 统计
        $where   = [];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['user_id', '=', $userinfo->id];
        $where[] = ['coupon_status', '=', 0];

        $where1   = [];
        $where1[] = ['hotel_id', '=', $hotel_id];
        $where1[] = ['user_id', '=', $userinfo->id];
        $where1[] = ['hongbao_status', '=', 0];

        $userinfo->quan_num             = Usercoupon::where($where)->count();
        $userinfo->hongbao_num          = HongbaoUser::where($where1)->count();
        $setting_list                   = \App\Models\Hotel\HotelSetting::getlists(['is_show_copyright', 'user_level_rule_decs'], $hotel_id);
        $userinfo->is_show_copyright    = isset($setting_list['is_show_copyright']) ? $setting_list['is_show_copyright'] : '-';
        $userinfo->user_level_rule_decs = isset($setting_list['user_level_rule_decs']) ? $setting_list['user_level_rule_decs'] : '';
        // 等级会员
        $userinfo->vipcard    = false;
        if (!empty($userinfo->vipId)) { // 付费会员
            $userinfo->vipcard = MemberVipSet::where(['id' => $userinfo->vipId, 'hotel_id' => $hotel_id])->first();
        }
        $userinfo->level0_icon = '';
        if(!empty($userinfo->level_id)){
            $userinfo->level_info = UserLevel::where(['hotel_id' => $hotel_id, 'level_num' => $userinfo->level_id])->first();
        }else{
            $userinfo->level0_icon = env('APP_URL').'/images/userlevel/level0.png';
        }


        return returnData(200, 1, ['userinfo' => $userinfo], 'ok');
    }


    // 获取用户订单
    public function getUserOrder(Request $request) {


    }

    // 绑定手机号
    public function bindPhone(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'phone'      => 'required',
                'phone_code' => 'required',
            ], [
                'phone.required'      => '手机号码 不能为空',
                'phone_code.required' => '手机验证码 不能为空',
            ]
        );

        $phone      = $request->get('phone');
        $phone_code = $request->get('phone_code');
        // 验证手机号码
        $serv = new \App\Services\SmsService();
        $ste  = $serv->isCodeCorrect($phone, $phone_code);
        if (!$ste) {
            return returnData(204, 0, [], '验证码不正确或已过期');
        }

        \App\User::where(['id' => $this->user->id])->update(['phone' => $phone]);
        return returnData(200, 1, [], '绑定成功');
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

    // 更新用户位置
    public function upLocationInfo(Request $request) {
        $user      = JWTAuth::parseToken()->authenticate();
        $longitude = $request->get('longitude');
        $latitude  = $request->get('latitude');
        $update    = [
            'longitude'     => $longitude,
            'latitude'      => $latitude,
            'province'      => $request->get('province'),
            'city'          => $request->get('city'),
            'region'        => $request->get('region'),
            'region_id'     => $request->get('region_id'),
            'address'       => $request->get('address'),
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
        ];
        $user      = (new \App\Services\UserService())->upLocationInfo($user->id, $update);
        return returnData(200, 1, [], 'ok');
    }


    // 生成签名
    protected function mapSig($data) {
        ksort($data);
        $newurl = http_build_query($data);

        $sig = md5('/ws/distance/v1/matrix?' . $newurl);
        return $sig;
    }

    // 加入酒店会员
    public function applyMember(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $request->validate(
            [
                'zxing' => 'required',
                'zname' => 'required',
                'phone' => 'required',
                'email' => 'nullable|email',
            ], [
                'zxing.required' => '姓氏 不能为空',
                'zname.required' => '名字 不能为空',
                'phone.required' => '手机号 不能为空',
                'email.email'    => '电子邮箱 格式不正确',
            ]
        );
        // 加入酒店会员
        $zxing   = $request->get('zxing');
        $zname   = $request->get('zname');
        $insdata = [
            'hotel_id' => $hotel_id,
            'uid'      => $user->id,
            'phone'    => $request->get('phone'),
            'name'     => $zxing . $zname,
        ];
        UserMember::addUser($insdata);
        return returnData(200, 1, [], 'ok');
    }


    // 获取开卡参数
    public function getAddCardParam(Request $request) {
        //$user        = JWTAuth::parseToken()->authenticate();
        $hotel_id  = $request->get('hotel_id');
        $WxCardTpl = WxCardTpl::where(['hotel_id' => $hotel_id])->first();
        if (empty($WxCardTpl->id)) {
            return returnData(205, 0, [], '商家未创建微信卡券');
        }
        $card_id = $WxCardTpl->card_id;

        $wxOpen = app('wechat.open');
        $gzhobj = $wxOpen->hotelWxgzh($hotel_id);
        $data   = [
            'card_id'   => $card_id,
            'outer_str' => 'minapp'
        ];
        $res1   = $gzhobj->card->member_card->getActivateUrl($data);
        if (!empty($res1['url'])) {
            $query = parse_url($res1['url'], PHP_URL_QUERY);
            parse_str($query, $params);
            $params['url'] = $res1['url'];
            return returnData(200, 1, $params, 'ok');
        }

        return returnData(205, 0, $res1, '未获取到开卡参数');
    }

    // 更新用户资料 支持多字段
    public function upUserProfile(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $request->validate(
            [
                'data' => 'required',
            ], [
                'data.required' => '字段数据不能为空',
            ]
        );
        $user_id   = $user->id;
        $field_arr = $request->get('data');
        $type      = $request->get('type', '');
        if (empty($field_arr)) {
            return returnData(205, 0, [], '字段数据不能为空');
        }
        if (!is_array($field_arr)) {
            return returnData(205, 0, [], '字段数据格式不正确');
        }
        $field_name_arr = ['nick_name', 'avatar'];
        $updata         = [];
        foreach ($field_arr as $key => $items) {
            if (!in_array($items['field_name'], $field_name_arr)) {
                return returnData(205, 0, [], '更新此项没有权限:' . $items['field_name']);
            }
            $updata[$items['field_name']] = $items['field_value'];
        }
        if(!empty($updata['nick_name'])){
            $nick_names = preg_replace('/[^\x{4e00}-\x{9fa5}A-Za-z0-9]/u', '', $updata['nick_name']);
            if(mb_strlen($nick_names) > 8){
                return returnData(205, 0, [], '昵称最多只能8位');
            }
            $updata['nick_name'] = $nick_names;
        }

        if ($type == 'up-avatar-nick') {
            $updata['is_upavatar'] = 1;
        }
        if (empty($updata)) {
            return returnData(205, 0, [], '更新字段为空,更新失败');
        }
        $res = \App\User::where(['id' => $user_id])->update($updata);

        if (!$res) {
            return returnData(205, 0, [], '更新失败');
        }
        return returnData(200, 1, [], 'ok');

    }

    // 设置支付密码
    public function setPayPassword(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $request->validate(
            [
                'pay_password'         => 'required|min:6',
                'confirm_pay_password' => 'required',
                'phone_code'           => 'required',
            ], [
                'pay_password.required'         => '支付密码 不能为空',
                'pay_password.min'              => '支付密码 最少6位数',
                'confirm_pay_password.required' => '确认支付密码 不能为空',
                'phone_code.required'           => '手机验证码 不能为空',
            ]
        );
        $pay_password         = $request->get('pay_password');
        $confirm_pay_password = $request->get('confirm_pay_password');
        $phone_code           = $request->get('phone_code');

        if ($pay_password != $confirm_pay_password) {
            return returnData(205, 0, [], '两次密码不相同,请检查');
        }
        // 验证手机号码
        $serv = new \App\Services\SmsService();
        $ste  = $serv->isCodeCorrect($user->phone, $phone_code);
        /*if (!$ste) {
            return returnData(204, 0, [], '验证码不正确或已过期');
        }*/
        $new_paypassword = md5($pay_password);
        $status          = User::where(['id' => $user->id])->update(['pay_password' => $new_paypassword]);

        return returnData(200, 1, [], '设置成功');
    }

    // 检查微信openid是否已经注册
    public function checkUserRegister(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $request->validate(
            [
                'wx_code' => 'required',
            ], [
                'wx_code.required' => '小程序code 不能为空',
            ]
        );
        $wx_code = $request->get('wx_code');
        $uid     = UserService::checkUserRegister($wx_code, $hotel_id);
        if ($uid !== false) {
            return returnData(200, 1, ['uid' => $uid], 'ok');
        }
        return returnData(200, 1, ['uid' => ''], 'ok');
    }

}
