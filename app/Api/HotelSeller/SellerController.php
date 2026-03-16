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

class SellerController extends BaseController {

    // 商家信息
    public function info(Request $request){

        return returnData(200, 1, ['info'=>''], 'ok');
    }


}
