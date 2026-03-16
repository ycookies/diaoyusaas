<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use App\Models\Hotel\HotelSetting;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;
use Zhuzhichao\IpLocationZh\Ip;

class HotelController extends BaseController {

    // 列表
    public function lists(Request $request) {
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $where = [];
        $where[] = ['id','=',218];
        $where[] = ['state','=',2];
        $list = Seller::where($where)
            ->select('id','hotel_user_id','name','address','ewm_logo','img')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list),'ok');
    }

    // 详情
    public function detail(Request $request) {
        $hotel_id = $request->get('hotel_id');
        //$hotelinfo = Seller::with('room')
        $hotelinfo = Seller::with('facilitys')->where('id', $hotel_id)
            ->select('id','hotel_user_id',
                'name','decorate_time','video_url','open_time',
                'address',
                'ewm_logo'
                ,'img','tel','coordinates')
            ->first();

        return returnData(200, 1, ['info' => $hotelinfo],'ok');
    }

    // 酒店资料
    public function infos(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $hotelinfo = Seller::where('id', $hotel_id)->first();
        return returnData(200, 1, ['info' => $hotelinfo],'ok');
    }


    // 获取小程序模板消息ID
    public function getTemplateMsgID(Request $request){
        $hotel_id = $request->get('hotel_id');
        $field_keys = $request->get('field_keys');
        if(empty($field_keys)){
            return returnData(204, 0, [],'消息模板key不能为空');
        }
        $field_arr = explode(',',$field_keys);
        if(empty($field_arr)){
            return returnData(204, 0, [],'消息模板key不能为空');
        }
        $setting = HotelSetting::getlists($field_arr,$hotel_id);
        $ids = [];
        foreach ($field_arr as $key => $field_name) {
            if(!empty($setting[$field_name])){
                $ids[] = $setting[$field_name];
            }
        }
        return returnData(200, 1, ['info' => $ids],'ok');
    }


}
