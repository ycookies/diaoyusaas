<?php

namespace App\Api\Hotel\Tuangou;

use App\Api\Hotel\BaseController;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use App\Models\Hotel\Order\Order;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 团购订单
class TuangouOrderController extends BaseController {

    // 订单列表
    public function orderList(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $hotel_id = $request->get('hotel_id');
        $status   = $request->get('status');
        $where = [
            ['hotel_id','=',$hotel_id],
            ['user_id','=',$user->id]
        ];
        $order_where = [];
        if(!empty($status)){
            if($status == 4){ // 待评价
                $where[] = ['order_status','=',3]; //已完成核对到店
                $order_where[] = ['is_comment','=',0];
            }else{
                $where[] =  ['order_status','=',$status];
            }
        }

        $list = TuangouOrderRelation::with('order', 'order.detail')
            ->where($where)
            ->when(!empty($order_where), function ($query) use ($order_where) {
                return $query->whereHas('order', function ($q) use ($order_where) {
                    $q->where($order_where);
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }


    // 订单详情
    public function orderDetail(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id         = $request->get('hotel_id');
        $tuangou_order_id = $request->get('tuangou_order_id');
        $order_no = $request->get('order_no');
        $where = [
            'hotel_id' => $hotel_id,
            ['user_id','=',$user->id]
        ];
        if(!empty($order_no)){
            $order_id =  Order::where(['hotel_id' => $hotel_id,'order_no'=> $order_no])->value('id');
            $where['order_id'] = $order_id;
        }
        if(!empty($tuangou_order_id) && is_numeric($tuangou_order_id)){
            $where['id'] = $tuangou_order_id;
        }
        if(count($where) <= 1){
            return returnData(404, 0, [], '订单编号信息有误');
        }

        $info             = TuangouOrderRelation::with('order', 'order.detail')
            ->where($where)
            ->first();
        $info->tickets_code = \App\Models\Hotel\TicketsCode::where(['order_no'=>$order_no])->get();
        if (!$info) {
            return returnData(404, 0, [], '找不到订单信息');
        }

        return returnData(200, 1, ['info' => $info], 'ok');

    }

}
