<?php

namespace App\Api\Portal;

use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;
use Zhuzhichao\IpLocationZh\Ip;

class HotelController extends BaseController {

    // 列表
    public function lists(Request $request) {
        //$ipinfo = Ip::find($request->ip());
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $where = [];
        $where[] = ['state','=',2];
        $where[] = ['seller_id','>',1000];
        // coordinates 经纬度;
        $lon = '';
        $lat = '';
        $list = Seller::where($where)
            ->select('id','seller_id','name','address','ewm_logo','img')
            ->when($request->has('lon'), function ($q, $v) {
                $request = Request();
                $q->selectRaw("id,lon,lat,
  ROUND(ST_DISTANCE(point(lon,lat),point({$request->lon},{$request->lat})) /0.0111,2) distance")
                    ->orderBy('distance','DESC');
            })
            /**/
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list),'ok');
    }

    // 详情
    public function detail(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $hotelinfo = Seller::with('room')
            ->select('id','seller_id','name','address','ewm_logo','img')
            ->where('id', $hotel_id)->first();

        return returnData(200, 1, ['info' => $hotelinfo],'ok');
        /*return [
            'hotel_name'    => $hotelinfo->name,
            'hotel_address' => $hotelinfo->address,
            'hotel_tel'     => $hotelinfo->tel,
            'coordinates'   => $hotelinfo->coordinates,
        ];*/
    }


}
