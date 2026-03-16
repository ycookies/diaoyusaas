<?php

namespace App\Api\Hotelold;

use App\Models\Hotel\Room;
use App\Models\Hotel\WxappConfig;
use App\Services\WxUserService;
use EasyWeChat\Factory;
use Illuminate\Http\Request as apiRequest;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Requests\Request;
use App\Http\Controllers\Controller;

class InfoController extends Controller {

    // 酒店详情
    public function detail(apiRequest $request){

        return returnData(200,1,[]);
    }

    // 酒店客房
    public function rooms(apiRequest $request){
        $seller_id = $request->seller_id;
        $where = [];
        $where[] = ['seller_id','=',$seller_id];
        $where[] = ['state','=',1];
        $list = Room::where($where)->select(['id','seller_id','name','logo','price','moreimg','floor','people','size','total_num'])->get();
        $data['list'] = $list;
        return returnData(200,1,$data);
    }

}
