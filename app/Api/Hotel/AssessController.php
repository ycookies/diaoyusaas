<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Assess;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\IntegralLog;
use App\User;
/**
 * 住客评价
 */
class AssessController extends BaseController {

    // 获取酒店评价列表
    public function getHoteAssessList(Request $request){
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);
        $where = [
            'hotel_id'=> $hotel_id
        ];

        if($pagesize == 1){
            $where['recommend'] = 1;
        }
        $list = Assess::with('user')->where($where)
            ->orderBy('id','DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取用户在这个酒店的评价列表
    public function getUserAssessList(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);

        $list = Assess::with('user','hotel')
            ->where(['hotel_id'=> $hotel_id,'user_id'=> $user->id])
            ->orderBy('id','DESC')
            ->paginate($pagesize);;
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 添加评价
    public function addAssess(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $order_no = $request->get('order_no');
        $request->validate(
            [
                'hotel_id'  => 'required',
                'order_no' => 'required',
                //'fileList'  => 'required',
                'pingjia_xin'  => 'required',
                'pingjia_content'  => 'required',

            ], [
                'hotel_id.required'   => '酒店ID 不能为空',
                'order_no.required'   => '订单编号 不能为空',
                //'fileList.required'   => '字段数据不能为空',
                'pingjia_xin.required'   => '评价星级 不能为空',
                'pingjia_content.required'   => '评价内容 不能为空',

            ]
        );

        $orderinfo = BookingOrder::where(['order_no' => $order_no,'hotel_id' => $hotel_id])->first();
        if(empty($orderinfo->id)){
            return returnData(205, 0, [], '未找到订单信息');
        }

        $pingjia_xin = $request->get('pingjia_xin');
        $pingjia_content = $request->get('pingjia_content');
        $img = $request->get('img');
        $ruzhu_date = date('Y.m',strtotime($orderinfo->dd_time));
        $insdata = [
            'hotel_id' => $hotel_id,
            'order_no' => $order_no,
            'user_id' => $user->id,
            'score' => $pingjia_xin,
            'content' => $pingjia_content,
            'ruzhu_date' => $ruzhu_date.'入驻',
            'room_id' => $orderinfo->room_id,
            'room_type' => $orderinfo->room_type,
        ];
        if(!empty($img)){
            $insdata['img'] = implode(',',$img);
        }
        $kk =  Assess::adds($insdata);

        // 更新订单 已评价
        BookingOrder::pingjia($order_no);

        // 增加积分
        User::addPoint($user->id,1,'住店评论奖励');

        return returnData(200, 1, [], 'ok');
    }

    // 修改评价
    public function editAssess(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');

        return returnData(200, 1, [], 'ok');
    }
}
