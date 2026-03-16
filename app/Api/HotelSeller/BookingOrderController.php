<?php

namespace App\Api\HotelSeller;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\BookingOrderClerk;
use Orion\Http\Requests\Request;
use chillerlan\QRCode\QRCode as QRCodes;
use Zxing\QrReader;
use App\Models\Hotel\TicketsCode;
use App\Models\Hotel\TicketsVerificationRecord;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;

class BookingOrderController extends BaseController {

    // 预定 订单列表(查询)
    public function lists(Request $request) {
        $seller   = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;

        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $where[]  = ['hotel_id', '=', $hotel_id];
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
                'code', 'is_confirm', 'confirm_time', 'voice',
                'room_type', 'seller_name', 'seller_address'
            )
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 预定 订单详情
    public function detail(Request $request) {
        $seller   = auth('sellerapi')->user();
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
                'code', 'is_confirm', 'confirm_time', 'voice',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        if (!$detail) {
            return returnData(500, 0, [], '找不到订单信息');
        }
        return returnData(200, 1, ['info' => $detail], 'ok');
    }

    // 预定确认
    public function confirm(Request $request) {
        $seller   = auth('sellerapi')->user();
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
                'code', 'is_confirm', 'confirm_time', 'voice',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        if (!$detail) {
            return returnData(500, 0, [], '找不到订单信息');
        }
        // 客房已满 预定取消 资金原路退回
        if ($confirm_type == 2) {
            event(new \App\Events\BookingOrderCancel($detail));
            return returnData(500, 0, [], '订单已取消,资金原路退回');
        }

        if ($detail->is_confirm == 1) {
            return returnData(500, 0, [], '订单已确认');
        }

        BookingOrder::orderConfirm($out_trade_no);

        return returnData(200, 1, [], '操作成功');
    }

    // 核销到店
    public function hexiao(Request $request) {
        info('到店核销',$request->all());
        $seller   = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;

        $request->validate(
            [
                'code' => 'required',
            ], [
                'code.required' => '订单核销码 不能为空',
            ]
        );

        $code    = $request->code;
        $where   = [];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['code', '=', $code];
        $detail  = BookingOrder::where($where)->first();

        if (!$detail) {
            return returnData(500, 0, [], '未找到订单信息');
        }
        // 已经核销
        if ($detail->voice == 2) {
            return returnData(205, 0, [], '已核销');
        }

        $model          = BookingOrder::find($detail->id);
        $model->voice   = 2;
        $model->dd_time = date('Y-m-d H:i:s');
        $model->save();

        return returnData(200, 1, ['info' => ''], '核销成功');
    }

    // 取消订单
    public function cancel(Request $request) {

        return returnData(200, 1, ['info' => ''], 'ok');
    }

    // 聚合所有业务订单，核对到店，离店通知
    public function orderConfirmAction(Request $request) {
        info('聚合所有业务订单核对到店',$request->all());
        //$seller   = auth('sellerapi')->user();
        $request->validate(
            [
                'out_trade_no' => 'required',
                //'check_code' => 'required',
                'confirm_type' => 'required',
            ], [
                'out_trade_no.required' => '订单编号 不能为空',
                'check_code.required'  => '到店码 不能为空',
                'confirm_type.required' => '确认类型 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $check_code  = $request->get('check_code');
        $confirm_type = $request->get('confirm_type');
        if ($confirm_type == 'dao_dian' && empty($check_code)) {
            return returnData(204, 0, [], '到店码 不能为空');
        }

        $where   = [];
        $where[] = ['ticket_code', '=', $check_code];
        $detail = TicketsCode::where($where)->first();
        if (!$detail) {
            return returnData(204, 0, [], '未找到订单信息');

        }
        // 已经核销
        if ($confirm_type == 'dao_dian' && $detail->status == 1) {
            return returnData(204, 0, [], '已核销，请勿重复操作');
        }
        if ($confirm_type == 'dao_dian'){
            // 进行核销 核销记录
            TicketsCode::verify($detail->order_no,$detail->ticket_code,1,TicketsVerificationRecord::Verifiy_type_arr[2]);

            // 团购业务订单
            if($detail->sign == \App\Models\Hotel\Order\Order::Sign_tuangou){
                $orderinfo = \App\Models\Hotel\Order\Order::where(['order_no' => $detail->order_no])->first();
                // 更新团购订单 已核销
                TuangouOrderRelation::upOrderStatus($orderinfo->id, TuangouOrderRelation::Order_status_3);

                // 核销后进行分账
                $service = new \App\Services\ProfitsharingService();
                $service->profitsharing($detail->order_no);

                return returnData(200, 1, [], '操作成功');
            }
        }




        // 订房业务订单 核销
        $where  = [
            ['out_trade_no', '=', $out_trade_no],
        ];
        $detail = BookingOrder::where($where)->first();
        if (!$detail) {
            return returnData(204, 0, [], '未找到此订单信息' . $out_trade_no);
        }

        // 核对到店
        if ($confirm_type == 'dao_dian') {
            $where2  = [
                ['out_trade_no', '=', $out_trade_no],
                ['code', '=', $check_code]
            ];
            $detail_daodian = BookingOrder::where($where2)->first();
            if (!$detail_daodian) {
                return returnData(204, 0, [], '到店码不正确,请检查到店码:' . $out_trade_no);
            }
            info('核对到店:'.$out_trade_no);
            // 解决到
            BookingOrder::daodian($out_trade_no, $check_code,1,BookingOrderClerk::Clerk_type_1);

            // 核对到店后进行分账
            $service = new \App\Services\ProfitsharingService();
            $service->profitsharingToBooking($out_trade_no);

        }
        // 客人离店
        if ($confirm_type == 'li_dian') {
            info('客人离店:'.$out_trade_no);
            BookingOrder::lidian($out_trade_no);
        }
        // 预定确认
        if ($confirm_type == 'confirm_order') {
            info('预定确认:'.$out_trade_no);
            BookingOrder::orderConfirm($out_trade_no);
        }

        return returnData(200, 1, [], '操作成功');
    }

    // 订房订单，核对到店，离店通知 todo 作废
    public function orderConfirmAction_old(Request $request) {
        $seller   = auth('sellerapi')->user();
        $request->validate(
            [
                'out_trade_no' => 'required',
                //'check_code' => 'required',
                'confirm_type' => 'required',
            ], [
                'out_trade_no.required' => '订单编号 不能为空',
                'check_code.required'  => '到店码 不能为空',
                'confirm_type.required' => '确认类型 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $check_code  = $request->get('check_code');
        $confirm_type = $request->get('confirm_type');
        if ($confirm_type == 'dao_dian' && empty($check_code)) {
            return returnData(204, 0, [], '到店码 不能为空');
        }

        $where  = [
            ['out_trade_no', '=', $out_trade_no],
        ];
        $detail = BookingOrder::where($where)->first();
        if (!$detail) {
            return returnData(204, 0, [], '未找到此订单信息' . $out_trade_no);
        }

        // 核对到店
        if ($confirm_type == 'dao_dian') {
            $where2  = [
                ['out_trade_no', '=', $out_trade_no],
                ['code', '=', $check_code]
            ];
            $detail_daodian = BookingOrder::where($where2)->first();
            if (!$detail_daodian) {
                return returnData(204, 0, [], '到店码不正确,请检查到店码:' . $out_trade_no);
            }
            info('核对到店:'.$out_trade_no);
            BookingOrder::daodian($out_trade_no, $check_code,$seller->id,BookingOrderClerk::Clerk_type_1);
        }
        // 客人离店
        if ($confirm_type == 'li_dian') {
            info('客人离店:'.$out_trade_no);
            BookingOrder::lidian($out_trade_no);
        }
        // 预定确认
        if ($confirm_type == 'confirm_order') {
            info('预定确认:'.$out_trade_no);
            BookingOrder::orderConfirm($out_trade_no);
        }

        return returnData(200, 1, [], '操作成功');
    }

    // 根据订单号生成小程序二维码
    public function getMinAppQrcode(Request $request){
        //info('获取小程序二维码',$request->all());
        $request->validate(
            [
                'sub_mch_id' => 'required',
                'out_trade_no' => 'required',
                'source' => 'required',
            ], [
                'sub_mch_id.required'  => '子商户号 不能为空',
                'out_trade_no.required' => '商家支付订单号 不能为空',
                'source.required'  => '订单来源 不能为空',
            ]
        );
        //$out_trade_no = '112024041710124473';
        //$sub_mch_id = '1644702947';
        $out_trade_no = $request->get('out_trade_no');
        $sub_mch_id = $request->get('sub_mch_id');
        $source = $request->get('source');
        $miniProgram = app('wechat.open')->submchidMiniProgram($sub_mch_id);
        if($miniProgram === false){
            return returnData(204, 0, [], '此商户号没有入驻融宝易住');
        }
        if($source == 'minapp'){
            $filenamefull = 'minapp-order-'.$out_trade_no.'.png';
            // 小程序码
            //$response = $miniProgram->app_code->getUnlimit('out_trade_no='.$out_trade_no,['page'=>'pages2/extend/pay_order_detail']);
            // 二维码
            $response = $miniProgram->app_code->getQrCode('/pages2/extend/pay_order_detail?out_trade_no='.$out_trade_no);
        }

        if($source == 'payisv'){
            $filenamefull = 'payisv-order-'.$out_trade_no.'.png';
            //$response = $miniProgram->app_code->getUnlimit('out_trade_no='.$out_trade_no,['page'=>'pages2/extend/pay_order_detail']);
            $response = $miniProgram->app_code->getQrCode('/pages2/extend/pay_order_detail?out_trade_no='.$out_trade_no);
        }

        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $full_path = public_path('uploads/images').'/'.$filenamefull;
            if(file_exists($full_path)){
                unlink($full_path);
            }
            $filename = $response->saveAs(public_path('uploads/images'), $filenamefull);
            if(!file_exists($full_path)){
                return returnData(204, 0, [], '保存小程序二维码失败');
            }
            $qrcode_content = env('APP_URL').'/qr/'.$sub_mch_id.'/'.$out_trade_no;
            $qrcode_url = env('APP_URL').'/uploads/images/'.$filenamefull;
            return returnData(200, 1, ['qrcode_content'=>$qrcode_content, 'qrcode_url'=>$qrcode_url], 'ok');
        }else{
            return returnData(204, 0, [], '生成小程序二维码失败');
        }
    }


}
