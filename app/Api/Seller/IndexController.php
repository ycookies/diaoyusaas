<?php

namespace App\Api\Seller;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Hotel\BookingOrder;

class IndexController extends BaseController {

    // 订单到店核销
    public function orderVerifying(Request $request){
        //$this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'hotel_id'       => 'required',
                'code' => 'required',
            ], [
                'hotel_id.required'       => '酒店ID 不能为空',
                'code.required'        => '订单核销码 不能为空',
            ]
        );
        $hotel_id = $request->hotel_id;
        $code = $request->code;
        $where = [];
        $where[] = ['hotel_id','=',$hotel_id];
        $where[] = ['code','=',$code];
        $info = BookingOrder::where($where)->first();
        // 已经核销
        if($info->voice == 2){
            return returnData(205, 0, [], '');
        }




    }

}
