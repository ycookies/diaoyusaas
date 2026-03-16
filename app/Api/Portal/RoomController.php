<?php

namespace App\Api\Portal;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;

class RoomController extends BaseController {

    // 列表
    public function lists(Request $request) {
        $this->user              = Auth::guard('api')->user();
        $home_pages['search']    = $this->search();
        $home_pages['banner']    = $this->banner();
        $home_pages['nav']       = $this->nav();
        $home_pages['notice']    = $this->notice();
        $home_pages['hotelinfo'] = $this->hotelInfo();
        $home_pages['topRoom']   = $this->topRoom();
        $home_pages['goods']     = $this->goods();
        return returnData(200, 1, ['home_pages' => $home_pages]);
    }

    // 详情
    public function detail() {
        $hotelinfo = Seller::where('user_id', $this->seller['seller_id'])->first();
        return [
            'hotel_name'    => $hotelinfo->name,
            'hotel_address' => $hotelinfo->address,
            'hotel_tel'     => $hotelinfo->tel,
            'coordinates'   => $hotelinfo->coordinates,
        ];
    }
}
