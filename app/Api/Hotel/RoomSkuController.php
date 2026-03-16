<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSheshi;
use App\Models\Hotel\RoomSheshiConfig;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomSkuGift;
use Orion\Http\Requests\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\CouponUser;
use DB;
use Carbon\Carbon;

// 房型销售sku
class RoomSkuController extends BaseController {

    // 获取房型列表
    public function getRoomLists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));

        $where    = [];
        $where[]  = ['hotel_id', '=', $hotel_id];
        $where[]  = ['state', '=', 1];
        $list     = Room::where($where)
            ->select('id', 'hotel_id', 'name', 'price', 'logo', 'moreimg', 'floor', 'people', 'bed', 'bed_num','area','bed_size', 'state')
            ->paginate($pagesize);

        foreach ($list as $key1 => &$item) {
            //$room_today_price = Roomprice::getRoomDateRangePrice($item->id, $start_time, $end_time);
            $item->room_yuan_low_price = ''; // 原价格
            $item->room_low_price = ''; //最新优惠低价
            $item->area_str = $item->area.'㎡';
            $item->bed_num_str = $item->bed_num.'张';
            $item->room_type_desc = $item->area.'㎡ '.$item->bed_num.'张床 可住'. $item->people.'人';

            if(empty($room_today_price)){
                $item->is_full_room = true;
            }
            $where    = [];
            $where[]  = ['hotel_id', '=', $hotel_id];
            $where[]  = ['state', '=', 1];
            $where[]  = ['room_id', '=', $item->id];
            $skulist     = RoomSkuPrice::where($where)->orderBy('roomsku_price', 'ASC')->get();
            // 获取sku的 销售价
            $room_sku_today_price_arr = [];
            if(!$skulist->isEmpty()){
                foreach ($skulist as $key => &$skuinfo) {

                    $room_sku_today_price = Roomprice::getRoomSkuDateRangePrice($skuinfo->id, $start_time, $end_time);
                    // 日期范围内的销售价
                    $skuinfo->room_sku_today_price = formatFloat($room_sku_today_price);
                    $room_sku_today_price_arr[] = $room_sku_today_price;
                    $skuinfo->roomsku_type_desc = $item->bed_num.'张床 可住'. $item->people.'人';

                    $skuinfo->roomsku_gift_list = [];
                    $skuinfo->roomsku_tags_list = [];
                    $skuinfo->roomsku_fuwu_arr = [];
                    // 礼包
                    if(!empty($skuinfo->roomsku_gift) && $skuinfo->roomsku_gift != '[]'){
                        $gift_list = RoomSkuGift::whereIn('id',$skuinfo->roomsku_gift)->limit(1)->get();
                        $skuinfo->roomsku_gift_list = $gift_list;
                    }

                    // 标签
                    if(!empty($skuinfo->roomsku_tags_str)){
                        $skuinfo->roomsku_tags_list = json_decode($skuinfo->roomsku_tags_str,true);
                    }
                    // 享受服务
                    if(!empty($skuinfo->roomsku_fuwu)){
                        $skuinfo->roomsku_fuwu_arr = json_decode($skuinfo->roomsku_fuwu,true);
                    }
                    
                    //$skuinfo->roomsku_title = $skuinfo->roomsku_zaocan_title.' | '.$skuinfo->roomsku_title;
                    // 是否满房
                    if(empty($skuinfo->roomsku_stock)){
                        $skuinfo->is_full_room = 1;
                    }
                }
                if(!empty($room_sku_today_price_arr)){
                    $item->room_low_price = formatFloat(min($room_sku_today_price_arr));
                }
                $item->room_sku = $skulist;
            }else{
                unset($list[$key1]);
            }


        }

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取房型详情
    public function getRoomDetail(Request $request) {
        $hotel_id              = $request->get('hotel_id');
        $room_id               = $request->get('room_id');
        $room_sku_id               = $request->get('room_sku_id');
        if(empty($room_sku_id)){
            return returnData(204, 0, [], '房型销售ID不能为空');
        }
        $sku_info = RoomSkuPrice::where(['hotel_id' => $hotel_id, 'id' => $room_sku_id])->first();
        if(!$sku_info){
            return returnData(204, 0, [], '没有找到房型相关信息');
        }
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));



        $info                  = Room::where(['hotel_id' => $hotel_id, 'id' => $sku_info->room_id])->first();
        $info->base_sheshi     = RoomSheshiConfig::where(['hotel_id' => $hotel_id])->get();

        $sku_info->roomsku_type_desc = $info->bed_num.'张床 可住'. $info->people.'人';
        $sku_info->roomsku_gift_list = [];
        $sku_info->roomsku_tags_list = [];
        $sku_info->roomsku_fuwu_arr = [];
        // 礼包
        if(!empty($sku_info->roomsku_gift) && $sku_info->roomsku_gift != '[]'){
            $gift_list = RoomSkuGift::whereIn('id',$sku_info->roomsku_gift)->limit(1)->get();
            $sku_info->roomsku_gift_list = $gift_list;
        }

        // 标签
        if(!empty($sku_info->roomsku_tags_str)){
            $sku_info->roomsku_tags_list = json_decode($sku_info->roomsku_tags_str,true);
        }
        // 享受服务
        if(!empty($sku_info->roomsku_fuwu)){
            $sku_info->roomsku_fuwu_arr = json_decode($sku_info->roomsku_fuwu,true);
        }
                    
        $info->roomsku_info = $sku_info; //销售信息
        $info->room_days_price = Roomprice::getRoomSkuDaysPricelist($room_sku_id, 0,90); //  日历价格
        $info->room_type_desc = $info->area.'㎡ '.$info->bed_num.'张床 可住'. $info->people.'人';
        $total_price = Roomprice::getRoomSkuDateRangePrice($room_sku_id, $start_time, $end_time);
        $sku_info->is_full_room = false;
        if(empty($total_price)){ // 是否满房
            $sku_info->is_full_room = true;
        }
        $room_today_price = !empty($total_price) ? $total_price:$info->roomsku_price; // 今日房价
        $info->room_today_price = formatFloat($room_today_price);

        info('sku-id：'.$room_sku_id,collect($info)->toArray());



        return returnData(200, 1, ['info' => $info], 'ok');
    }

    // 列表 sku列表
    public function lists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $room_id = $request->get('room_id');

        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));

        $where    = [];
        $where[]  = ['hotel_id', '=', $hotel_id];
        $where[]  = ['state', '=', 1];
        $list     = RoomSkuPrice::where($where)
            ->paginate($pagesize);

        foreach ($list as $key => &$item) {
            $room_today_price = Roomprice::getRoomSkuDateRangePrice($item->id, $start_time, $end_time);

            $item->is_full_room = false;
            if(empty($item->roomsku_stock)){
                $item->is_full_room = true;
            }
            $item->room_info = Room::where(['id'=>$item->room_id])
                ->select('id', 'hotel_id', 'name', 'price', 'logo', 'moreimg', 'floor', 'people', 'bed', 'bed_num','area','bed_size', 'state')->first();

            $item->room_today_price = !empty($room_today_price) ? $room_today_price:$item->price; // 今日房价
        }

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 详情 sku 详情
    public function detail(Request $request) {
        $hotel_id              = $request->get('hotel_id');
        $room_id               = $request->get('room_id');
        $room_sku_id               = $request->get('room_sku_id');
        $start_time = $request->get('start_time',date('Y-m-d'));
        $end_time   = $request->get('end_time',date('Y-m-d',strtotime('+1 days')));

        $room_sku_info = RoomSkuPrice::with('room')->where(['hotel_id' => $hotel_id, 'id' => $room_sku_id])->first();
        if(!$room_sku_info){
            return returnData(204, 0, [], '未找到房型客房信息');
        }
        //$info                  = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();

        $room_sku_info->base_sheshi     = RoomSheshi::where(['hotel_id' => $hotel_id])->get();
        $room_sku_info->room_days_price = Roomprice::getRoomSkuDaysPricelist($room_sku_id, 0,90); //  日历价格

        $total_price = Roomprice::getRoomSkuDateRangePrice($room_sku_id, $start_time, $end_time);
        $room_sku_info->is_full_room = false;
        if(empty($total_price)){ // 是否满房
            $room_sku_info->is_full_room = true;
        }
        $room_today_price = !empty($total_price) ? $total_price:$room_sku_info->roomsku_price; // 今日房价
        $room_sku_info->room_today_price = $room_today_price;

        return returnData(200, 1, ['info' => $room_sku_info], 'ok');
    }

    // 获取客房日历价
    public function getRoomSkuCalendarPrice(Request $request){
        $hotel_id   = $request->get('hotel_id');
        $room_id    = $request->get('room_id');
        $room_sku_id    = $request->get('room_sku_id');
        if(empty($room_sku_id)){
            return returnData(204, 0, [], '房型客房ID 不能为空');
        }
        $list = Roomprice::getRoomSkuCalendarPrice($hotel_id,$room_sku_id, 0,90);

        return returnData(200, 1, ['info' => $list], 'ok');
    }


    // 获取预定日期内的价格
    public function getSkuBookingRangeTotalPrice(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();;
        $hotel_id   = $request->get('hotel_id');
        $room_id    = $request->get('room_id');

        $room_sku_id               = $request->get('room_sku_id');
        if(empty($room_sku_id)){
            return returnData(204, 0, [], '房型销售ID不能为空');
        }
        $sku_info = RoomSkuPrice::where(['hotel_id' => $hotel_id, 'id' => $room_sku_id])->first();
        if(!$sku_info){
            return returnData(204, 0, [], '没有找到房型相关信息');
        }

        $start_time = $request->get('start_time');
        $end_time   = $request->get('end_time');
        $booking_num   = $request->get('booking_num',1);
        $info       = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();

        // 检查是否有剩余客房
        $is_full_room = RoomSkuPrice::getBookingSkuRangeIsFM($room_sku_id,$start_time,$end_time);
        if($is_full_room !== true){
            return returnData(205, 0, [], $is_full_room);
        }

        $lists =  Roomprice::getRoomSkuBookingRangeYouhuiPrice($user->id,$hotel_id,$room_sku_id,$start_time,$end_time,$booking_num);
        return returnData(200, 1, $lists, 'ok');
    }
}
