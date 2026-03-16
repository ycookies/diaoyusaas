<?php

namespace App\Api\HotelSeller;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;

// 不需要auth 对外提供的接口
class DailyActionController extends BaseController {

    // 预定 订单列表(查询)
    public function lists(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;

        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $where[]    = ['hotel_id', '=', $hotel_id];
        if (!empty($request->status)) {
            $where[] = ['status', '=', $request->status];
        }
        if (!empty($request->start_time)) {
            $where[] = ['created_at', '>=', $request->start_time];
        }
        if (!empty($request->end_time)) {
            $where[] = ['created_at', '<=', $request->end_time];
        }
        $list = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code','is_confirm','confirm_time','voice',
                'room_type', 'seller_name', 'seller_address'
            )
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 预定 订单详情
    public function detail(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;
        $request->validate(
            [
                'out_trade_no' => 'required',
            ], [
                'out_trade_no.required' => '订单号 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $where[]      = ['hotel_id', '=', $hotel_id];
        $where[]      = ['out_trade_no', '=', $out_trade_no];
        $detail       = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code','is_confirm','confirm_time','voice',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        if(!$detail){
            return returnData(500, 0, [], '找不到订单信息');
        }
        return returnData(200, 1, ['info' => $detail], 'ok');
    }

    // 预定确认
    public function confirm(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;

        $request->validate(
            [
                'out_trade_no' => 'required',
                'confirm_type' => 'required',
            ], [
                'out_trade_no.required' => '订单号 不能为空',
                'confirm_type.required' => '确认类型 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $confirm_type = $request->get('confirm_type');
        $where[]      = ['hotel_id', '=', $hotel_id];
        $where[]      = ['out_trade_no', '=', $out_trade_no];
        $detail       = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code','is_confirm','confirm_time','voice',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        if(!$detail){
            return returnData(500, 0, [], '找不到订单信息');
        }
        // 客房已满 预定取消 资金原路退回
        if($confirm_type == 2){
            event(new \App\Events\BookingOrderCancel($detail));
            return returnData(500, 0, [], '订单已取消,资金原路退回');
        }

        if($detail->is_confirm == 1){
            return returnData(500, 0, [], '订单已确认');
        }
        $model = BookingOrder::find($detail->id);
        $model->is_confirm = 1;
        $model->confirm_time = date('Y-m-d H:i:s');
        $model->save();

        // 给用户发送通知
        event(new \App\Events\BookingOrderConfirm($detail));

        return returnData(200, 1, [], '操作成功');
    }

    // 核销到店
    public function hexiao(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;

        $request->validate(
            [
                'code' => 'required',
            ], [
                'code.required'        => '订单核销码 不能为空',
            ]
        );

        $code = $request->code;
        $where = [];
        $where[] = ['hotel_id','=',$hotel_id];
        $where[] = ['code','=',$code];
        $detail = BookingOrder::where($where)->first();

        if(!$detail){
            return returnData(500, 0, [], '未找到订单信息');
        }
        // 已经核销
        if($detail->voice == 2){
            return returnData(205, 0, [], '已核销');
        }

        $model = BookingOrder::find($detail->id);
        $model->voice = 2;
        $model->confirm_time = date('Y-m-d H:i:s');
        $model->save();

        return returnData(200, 1, ['info'=>''], '核销成功');
    }

    // 取消订单
    public function cancel(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }



}
