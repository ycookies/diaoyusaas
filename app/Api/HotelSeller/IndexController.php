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

class IndexController extends BaseController {

    // 首页统计
    public function home(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }

    // 酒店信息
    public function hotelinfo(Request $request){
        $seller = auth('sellerapi')->user();
        $hotel_id = $seller->hotel_id;
        $hotelinfo = Seller::with('room')
            ->select('id','hotel_user_id','name','address','ewm_logo','img')
            ->where('id', $hotel_id)->first();

        return returnData(200, 1, ['info' => $hotelinfo],'ok');
    }
}
