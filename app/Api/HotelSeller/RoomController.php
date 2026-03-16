<?php

namespace App\Api\HotelSeller;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;

class RoomController extends BaseController {

    // 客房列表
    public function lists(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $where = [];
        $where[] = ['hotel_id','=',$hotel_id];
        $where[] = ['state','=',1];
        $list = Room::where($where)
            ->select('id','hotel_id','name','price','logo','moreimg','floor','people','bed','bed_num','state')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list),'ok');
    }

    // 客房详情
    public function detail(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;
        $room_id = $request->get('room_id');
        $info = Room::where(['hotel_id'=> $hotel_id,'id'=> $room_id])->first();
        return returnData(200, 1, ['info'=> $info],'ok');
    }

    // 某个客房调价
    public function batchToPrice(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }

    // 客房开关（上线，下线，已订满）
    public function actions(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }

    // 获取客房日期价格
    public function getDatePrice(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }


}
