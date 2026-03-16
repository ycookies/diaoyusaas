<?php

namespace App\Api\Hotel\Order;


use App\Api\Hotel\BaseController;
use App\Models\Hotel\Order\Order;
use App\Models\Hotel\Order\OrderComment;
use App\Models\Hotel\Order\OrderDetail;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 订单评价
class OrderCommentController extends BaseController {

    // 评价列表
    public function commentList(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $is_top   = $request->get('is_top');
        $sign     = $request->get('sign');
        $where    = [
            'hotel_id' => $hotel_id,
            'sign'     => $sign,
        ];

        if (!empty($is_top)) {
            $where['is_top'] = 1;
        }
        $list = OrderComment::with('order_detail', 'user')->where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }


    // 评价详情
    public function commentDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $order_no = $request->get('order_no');
        $request->validate(
            [
                'hotel_id' => 'required',
                'order_no' => 'required',

            ], [
                'hotel_id.required' => '酒店ID 不能为空',
                'order_no.required' => '订单编号 不能为空',
            ]
        );
        $info = Order::with('comment')->where(['order_no' => $order_no, 'hotel_id' => $hotel_id])->first();

        if (empty($info->id)) {
            return returnData(205, 0, [], '未找到订单信息');
        }
        //$info = OrderComment::where(['order_id' => $orderinfo->id ])->get();

        return returnData(200, 1, ['info' => $info], 'ok');
    }


    // 增加评价
    public function addComment(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $order_no = $request->get('order_no');
        $request->validate(
            [
                'hotel_id'        => 'required',
                'order_no'        => 'required',
                //'fileList'  => 'required',
                'pingjia_xin'     => 'required',
                'pingjia_content' => 'required',

            ], [
                'hotel_id.required'        => '酒店ID 不能为空',
                'order_no.required'        => '订单编号 不能为空',
                //'fileList.required'   => '字段数据不能为空',
                'pingjia_xin.required'     => '评价星级 不能为空',
                'pingjia_content.required' => '评价内容 不能为空',

            ]
        );

        $orderinfo = Order::where(['order_no' => $order_no, 'hotel_id' => $hotel_id])->first();
        if (empty($orderinfo->id)) {
            return returnData(205, 0, [], '未找到订单信息');
        }

        $pingjia_xin     = $request->get('pingjia_xin');
        $pingjia_content = $request->get('pingjia_content');
        $img             = $request->get('img');
        $ruzhu_date      = date('Y.m', strtotime($orderinfo->dd_time));
        $order_detail_id = OrderDetail::where(['order_id' => $orderinfo->id])->value('id');
        $insdata         = [
            'hotel_id'        => $hotel_id,
            'order_id'        => $orderinfo->id,
            'order_detail_id' => $order_detail_id,
            'user_id'         => $user->id,
            'score'           => $pingjia_xin,
            'content'         => $pingjia_content,
            'sign'            => $orderinfo->sign
        ];
        if (!empty($img)) {
            $insdata['pic_url'] = implode(',', $img);
        }
        $kk = OrderComment::adds($insdata);

        // 更新订单 已评价
        Order::pingjia($order_no);

        // 业务订单状态
        if ($orderinfo->slug == Order::Sign_tuangou) { // 团购
            TuangouOrderRelation::upOrderStatus($orderinfo->id, TuangouOrderRelation::Order_status_4);
        }

        // 增加积分
        User::addPoint($user->id, 1, '评论奖励');

        return returnData(200, 1, [], '提交成功');
    }


    // 修改评价
    public function editComment(Request $request) {

    }

}
