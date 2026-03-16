<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Huodong;
use App\Models\Hotel\HuodongUser;
use App\Models\Hotel\HuodongOrder;
use App\Services\ParkingService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 活动报名
class HuodongController extends BaseController
{

    // 获取活动列表
    public function getLists(Request $request)
    {
        $hotel_id = $request->get('hotel_id');
        $wx_code     = $request->get('wx_code');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        // 通过code 获取用户信息
        $userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);
        // 微信获取失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }
        $user_id = $userinfo->id;
        $where    = [
            'hotel_id' => $hotel_id,
            'is_active' => 1,
        ];
        $list     = Huodong::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取活动详情
    public function getDetail(Request $request)
    {
        $hotel_id   = $request->get('hotel_id');
        $act_code = $request->get('act_code');
        // with('baoming')
        $info = Huodong::where(['hotel_id' => $hotel_id, 'act_code' => $act_code])
            ->first();

        return returnData(200, 1, ['info' => $info], 'ok');
    }

    // 获取用户参于活动列表
    public function getUserHuodong(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id   = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $where    = [
            'hotel_id' => $hotel_id,
            'user_id' => $user->id,
        ];
        $list = HuodongUser::with('user','huodong')
        ->where($where)
        ->orderBy('id', 'DESC')
        ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取用户参于活动报名订单
    public function getUserHuodongOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id   = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $where    = [
            'hotel_id' => $hotel_id,
            'user_id' => $user->id,
        ];
        $list = HuodongOrder::with('user','huodong')
        ->where($where)
        ->orderBy('id', 'DESC')
        ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 活动报名
    public function baomingSave(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'hotel_id' => 'required',
                'act_code'  => 'required',
                'baoming_name'    => 'required',
                'baoming_phone'    => 'required',
                //'remarks'  => 'required',
            ],
            [
                'hotel_id.required' => '酒店ID 不能为空',
                'act_code.required'  => '活动ID 不能为空',
                'baoming_name.required'    => '参加活动人名 不能为空',
                'baoming_phone.required'    => '参加活动联系电话 不能为空',
            ]
        );

        $hotel_id = $request->get('hotel_id');
        $act_code    = $request->get('act_code');
        $bm_name  = $request->get('baoming_name');
        $bm_phone  = $request->get('baoming_phone');
        $wx_code    = $request->get('wx_code');


        // 通过code 获取用户信息
        $userinfo = $user;
        //$userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);
        // 微信登陆失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }

        $info = Huodong::where(['hotel_id' => $hotel_id, 'act_code' => $act_code])->first();
        if (empty($info)) {
            $errormsg = '未查找到活动信息';
            return returnData(204, 0, [], $errormsg);
        }
        if (empty($info->is_active)) {
            $errormsg = '活动已经结束';
            return returnData(204, 0, [], $errormsg);
        }

        //  检查是否已经报满
        $counts = HuodongUser::where(['hotel_id' => $hotel_id, 'hd_id' => $info->id])->count();

        if ($info->act_rensu > 0 && $counts  >= $info->act_rensu) {
            return returnData(204, 0, [], '活动报名人数已满');
        }
        $is_bm = HuodongUser::where(['hotel_id' => $hotel_id, 'hd_id' => $info->id, 'user_id' => $userinfo->id])->count();
        if (!empty($is_bm)) {
            return returnData(204, 0, [], '您已经报名了');
        }
        // 免费活动
        if (empty($info->act_cost)) {
            HuodongUser::addbm($hotel_id, $info->id, $userinfo->id,$bm_name,$bm_phone);
            return returnData(200, 1, [], '报名成功');
        }

        $order_no = 'HD' . $hotel_id . date('YmdHis');
        $amount     = $info->act_cost;
        $subject    = '参入活动缴纳报名费';
        $openid     = $userinfo->openid;

        // 创建订单
        $insdata = [
            'hotel_id'      => $hotel_id,
            'user_id'       => $userinfo->id,
            'order_no'     => $order_no,
            'hd_id'     => $info->id,
            'pay_amount'     => $amount,
            'pay_status'     => 0,
            'bm_info'=> json_encode(['bm_name'=>$bm_name,'bm_phone'=>$bm_phone]),
        ];
        HuodongOrder::addOrder($insdata);
        $res =  app('wechat.isvpay')->isvPay($hotel_id,$order_no, $amount, $subject, $openid,0);
        return $res;

    }
}
