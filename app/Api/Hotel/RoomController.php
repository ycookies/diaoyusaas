<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSheshi;
use Orion\Http\Requests\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\CouponUser;
use DB;
use Carbon\Carbon;

class RoomController extends BaseController {

    // 列表
    public function lists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));

        $where    = [];
        $where[]  = ['hotel_id', '=', $hotel_id];
        $where[]  = ['state', '=', 1];
        $list     = Room::where($where)
            ->select('id', 'hotel_id', 'name', 'price', 'logo', 'moreimg', 'floor', 'people', 'bed', 'bed_num', 'state')
            ->paginate($pagesize);

        foreach ($list as $key => &$item) {
            $room_today_price = Roomprice::getRoomDateRangePrice($item->id, $start_time, $end_time);
            $item->is_full_room = false;
            if(empty($room_today_price)){
                $item->is_full_room = true;
            }
            $item->room_today_price = !empty($room_today_price) ? $room_today_price:$item->price; // 今日房价
        }

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 详情
    public function detail(Request $request) {
        $hotel_id              = $request->get('hotel_id');
        $room_id               = $request->get('room_id');
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));

        $info                  = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();
        if(!$info){
            return returnData(204, 0, [], '找不到此房型信息');
        }
        $info->base_sheshi     = RoomSheshi::where(['hotel_id' => $hotel_id])->get();
        $info->room_days_price = Roomprice::getRoomDaysPricelist($room_id, 0,90); //  日历价格

        $total_price = Roomprice::getRoomDateRangePrice($room_id, $start_time, $end_time);
        $info->is_full_room = false;
        if(empty($total_price)){ // 是否满房
            $info->is_full_room = true;
        }
        $room_today_price = !empty($total_price) ? $total_price:$info->price; // 今日房价
        $info->room_today_price = $room_today_price;
        return returnData(200, 1, ['info' => $info], 'ok');
    }
    // 获取客房日历价
    public function getRoomCalendarPrice(Request $request){
        $hotel_id   = $request->get('hotel_id');
        $room_id    = $request->get('room_id');
        if(empty($room_id)){
            return returnData(204, 0, [], '房型ID 不能为空');
        }
        $list = Roomprice::getRoomCalendarPrice($hotel_id,$room_id, 0,90);
        return returnData(200, 1, ['info' => $list], 'ok');
    }


    // 获取预定日期内的价格
    public function getBookingRangeTotalPrice(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();;
        $hotel_id   = $request->get('hotel_id');
        $room_id    = $request->get('room_id');
        $start_time = $request->get('start_time');
        $end_time   = $request->get('end_time');
        $booking_num   = $request->get('booking_num',1);
        $info       = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();

        // 检查是否有剩余客房
        $is_full_room = Room::getBookingRangeIsFM($room_id,$start_time,$end_time);
        if($is_full_room !== true){
            return returnData(205, 0, [], $is_full_room);
        }

        $lists =  Roomprice::getRoomBookingRangeYouhuiPrice($user->id,$hotel_id,$room_id,$start_time,$end_time,$booking_num);
        return returnData(200, 1, $lists, 'ok');
        // 作废
        $total_price = Roomprice::getRoomDateRangePrice($room_id, $start_time, $end_time);
        if(empty($total_price)){
            $total_price = $info->price;
        }
        $data = [
            'youhui_price' => '',
            'yuan_price' => $total_price,
            'total_price' => $total_price,
            'coupon_list' => [],
        ];
        if($request->get('is_coupon','') == 'no'){
            return $data;
        }

        // 查看优惠券
        $where = [
            ['user_id','=',$user->id],
            ['hotel_id','=',$hotel_id],
            ['coupon_status','=',0],
            ['expire_time','>=',date('Y-m-d H:i:s')],
        ];
        $coupon_user = CouponUser::where($where)->first();
        if($coupon_user){
            $couponinfo = Coupon::where(['id' =>$coupon_user->coupon_id])->first();
            if(!empty($couponinfo->id)){
                if(bccomp($total_price,$couponinfo->need_cost) == -1){ // 如何小于，则没有满足金额

                }else{
                    $yuan_price = $total_price;
                    $pay_price = bcsub($total_price,$couponinfo->cost,2);
                    $data = [
                        'coupon_list' => Coupon::where(['id' =>$coupon_user->coupon_id])->get(),
                        'youhui_price' => $couponinfo->cost,
                        'yuan_price' => $yuan_price,
                        'total_price' => $pay_price,
                    ];
                }
            }
        }
        //$info->base_sheshi = RoomSheshi::where(['hotel_id'=>$hotel_id])->get();

        return returnData(200, 1, $data, 'ok');
    }
}
