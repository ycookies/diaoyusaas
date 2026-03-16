<?php

namespace App\Api\Hotel\Tuangou;

use App\Models\Hotel\BalanceLog;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Order\OrderComment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Api\Hotel\BaseController;

// 团购订单评价
class TuangouOrderCommentController extends BaseController {

    // 评价列表
    public function commentList(Request $request){
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);
        $where = [
            'hotel_id'=> $hotel_id
        ];
        if($pagesize == 1){
            $where['recommend'] = 1;
        }
        $list = OrderComment::with('user')->where($where)
            ->orderBy('id','DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }


    // 评价详情
    public function commentDetail(Request $request){
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);
        $where = [
            'hotel_id'=> $hotel_id
        ];
        if($pagesize == 1){
            $where['recommend'] = 1;
        }
        $list = OrderComment::with('user')->where($where)
            ->orderBy('id','DESC')
            ->paginate($pagesize);
    }


    // 增加评价
    public function addComment(Request $request){

    }


    // 修改评价
    public function editComment(Request $request){

    }

}
