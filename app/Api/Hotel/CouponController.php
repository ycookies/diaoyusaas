<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\Usercoupon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


/**
 * 优惠券
 */
class CouponController extends BaseController {

    // 获取可领取优惠券列表
    public function getLists(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $type     = $request->get('type', 0);
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);

        $where   = [];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['status', '=', 1];
        $where[] = ['type', '=', $type];
        $where[] = ['start_time', '>=', date('Y-m-d 00:00:00')];
        $list    = Coupon::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        // 用户是否有领取
        foreach ($list as $key => &$items) {
            $is_receive = 0;
            if (!empty($user->id)) {
                $counts = Usercoupon::where(['coupon_id' => $items->id, 'user_id' => $user->id, 'hotel_id' => $hotel_id])->count();
                if ($counts) {
                    unset($list[$key]);
                    $is_receive = 1;
                }
            } else {
                $items->is_receive = 0;
            }


        }

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取用户优惠券
    public function getUserLists(Request $request) {
        $user          = JWTAuth::parseToken()->authenticate();
        $hotel_id      = $request->get('hotel_id');
        $coupon_status = $request->get('coupon_status');
        $page          = $request->get('page');
        $pagesize      = $request->get('pagesize', 10);

        $where   = [];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['user_id', '=', $user->id];
        $where[] = ['coupon_status', '=', $coupon_status];
        $list    = Usercoupon::with('coupon')->where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 领取优惠券
    public function receive(Request $request) {
        $user      = JWTAuth::parseToken()->authenticate();
        $hotel_id  = $request->get('hotel_id');
        $coupon_id = $request->get('coupon_id');
        $where     = [];
        $where[]   = ['id', '=', $coupon_id];
        $where[]   = ['hotel_id', '=', $hotel_id];
        //$where[] = ['status' ,'=', 1];
        //$where[] = ['end_time' ,'<=', date('Y-m-d 23:59:59')];

        $info = Coupon::where($where)->first();
        if (!$info) {
            return returnData(204, 0, [], '未找到优惠券信息');
        }
        if ($info->status != 1) {
            return returnData(204, 0, [], '优惠券已无效,无法领取');
        }
        if (!$info->end_time > date('Y-m-d 23:59:59')) {
            return returnData(204, 0, [], '优惠券已过期,无法领取');
        }
        if ($info->number <= 0) {
            return returnData(204, 0, [], '优惠券已发放完,无法领取');
        }
        $where  = [
            'user_id'   => $user->id,
            'hotel_id'  => $hotel_id,
            'coupon_id' => $coupon_id,
        ];
        $counts = Usercoupon::where($where)->count();
        if ($counts) {
            return returnData(204, 0, [], '已领取过');
        }

        // 开启事务
        \DB::beginTransaction();
        try {
            $insdata = [
                'user_id'       => $user->id,
                'hotel_id'      => $hotel_id,
                'coupon_id'     => $coupon_id,
                'expire_time'   => $info->end_time,
                'coupon_status' => 0,
            ];

            Usercoupon::receive($insdata);
            \DB::commit();
            return returnData(200, 1, ['info' => $info], 'ok');
        } catch (\Error $error) {
            $error_msg = [
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'msg'  => $error->getMessage(),
            ];
            \DB::rollBack();
            return returnData(500, 0, $error_msg, '系统繁忙,请稍候重试!');
        } catch (\Exception $exception) {
            $error_msg = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'msg'  => $exception->getMessage(),
            ];
            \DB::rollBack();
            return returnData(500, 0, $error_msg, '系统繁忙,请稍候重试!');
        }
    }

    // 收藏小程序 发放优惠券活动
    public function minappFavSendConpon(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        info('收藏小程序 发放优惠券活动',[$user->id]);
        $where   = [];
        $where[] = ['grant_type', '=', 2];
        $where[] = ['hotel_id', '=', $hotel_id];
        //$where[] = ['status' ,'=', 1];
        //$where[] = ['end_time' ,'<=', date('Y-m-d 23:59:59')];

        $info = Coupon::where($where)->first();
        if (!$info) {
            return returnData(204, 0, [], '收藏小程序送优惠券活动 未开始');
        }
        if ($info->status != 1) {
            return returnData(204, 0, [], '收藏小程序送优惠券活动已关闭,无法领取');
        }
        if (!$info->end_time > date('Y-m-d 23:59:59')) {
            return returnData(204, 0, [], '收藏小程序送优惠券活动 已过期,无法领取');
        }
        if ($info->number <= 0) {
            return returnData(204, 0, [], '收藏小程序送优惠券活动 已发放完,无法领取');
        }
        $coupon_id = $info->id;
        $where     = [
            'user_id'   => $user->id,
            'hotel_id'  => $hotel_id,
            'coupon_id' => $coupon_id,
        ];
        $counts    = Usercoupon::where($where)->count();
        if ($counts) {
            return returnData(204, 0, [], '已领取过');
        }

        // 开启事务
        \DB::beginTransaction();
        try {
            $insdata = [
                'user_id'       => $user->id,
                'hotel_id'      => $hotel_id,
                'coupon_id'     => $coupon_id,
                'expire_time'   => $info->end_time,
                'coupon_status' => 0,
            ];
            Usercoupon::receive($insdata);
            \DB::commit();
            return returnData(200, 1, ['info' => $info], '已发放');
        } catch (\Error $error) {
            $error_msg = [
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'msg'  => $error->getMessage(),
            ];
            \DB::rollBack();
            return returnData(500, 0, $error_msg, '系统繁忙,请稍候重试!');
        } catch (\Exception $exception) {
            $error_msg = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'msg'  => $exception->getMessage(),
            ];
            \DB::rollBack();
            return returnData(500, 0, $error_msg, '系统繁忙,请稍候重试!');
        }
    }
}
