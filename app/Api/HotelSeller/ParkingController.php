<?php

namespace App\Api\HotelSeller;

use App\Models\Hotel\ParkingOrder;
use App\Services\ParkingService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 停车场
class ParkingController extends BaseController {

    // 获取车辆入场记录
    public function getParkingCarIn(Request $request) {
        $sub_mch_id = $request->get('sub_mch_id');
        if(empty($sub_mch_id)){
            return returnData(205, 0, [], '商家 sub_mch_id 不能为空');
        }
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $start_time    = $request->get('start_time',date('Y-m-d H:i:s',strtotime('-30 days')));
        $end_time     = $request->get('end_time',date('Y-m-d 23:59:59'));

        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        if(empty($oauthinfo->hotel_id)){
            return returnData(205, 0, [], '此商家还没有入驻平台,请检查!');
        }
        $hotel_id = $oauthinfo->hotel_id;
        // 获取停车场ID
        $service = new ParkingService($hotel_id);
        $data = [
            'pageNum' => $page,
            'pageSize' => $pagesize,
            'startTime' => $start_time,
            'endTime' => $end_time,
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getCarIn',$data);
        $res = json_decode($res,true);

        $resdatak = [
            'recordList' => [],
            'recordCount'=> 0,
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        if(empty($res['result']['recordList'])){
            return returnData(200, 1, $resdatak, 'ok');
        }
        $resdatak = [
            'recordList' => $res['result']['recordList'],
            'recordCount'=> $res['result']['recordCount'],
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        return returnData(200, 1, $resdatak, 'ok');
    }

    // 获取停车场出场记录
    public function getParkingCarOut(Request $request) {
        $sub_mch_id = $request->get('sub_mch_id');
        if(empty($sub_mch_id)){
            return returnData(205, 0, [], '商家 sub_mch_id 不能为空');
        }
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $start_time    = $request->get('start_time',date('Y-m-d H:i:s',strtotime('-30 days')));
        $end_time     = $request->get('end_time',date('Y-m-d 23:59:59'));

        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        if(empty($oauthinfo->hotel_id)){
            return returnData(205, 0, [], '此商家还没有入驻平台,请检查!');
        }
        $hotel_id = $oauthinfo->hotel_id;
        // 获取停车场ID
        $service = new ParkingService($hotel_id);
        $data = [
            'pageNum' => $page,
            'pageSize' => $pagesize,
            'startTime' => $start_time,
            'endTime' => $end_time,
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getCarOut',$data);
        $res = json_decode($res,true);

        $resdatak = [
            'recordList' => [],
            'recordCount'=> 0,
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        if(empty($res['result']['recordList'])){
            return returnData(200, 1, $resdatak, 'ok');
        }
        $resdatak = [
            'recordList' => $res['result']['recordList'],
            'recordCount'=> $res['result']['recordCount'],
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        return returnData(200, 1, $resdatak, 'ok');
    }

    // 获取停车场收费明细
    public function getParkingChargeInfo(Request $request) {
        $sub_mch_id = $request->get('sub_mch_id');
        if(empty($sub_mch_id)){
            return returnData(205, 0, [], '商家 sub_mch_id 不能为空');
        }
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $start_time    = $request->get('start_time',date('Y-m-d H:i:s',strtotime('-30 days')));
        $end_time     = $request->get('end_time',date('Y-m-d 23:59:59'));

        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        if(empty($oauthinfo->hotel_id)){
            return returnData(205, 0, [], '此商家还没有入驻平台,请检查!');
        }
        $hotel_id = $oauthinfo->hotel_id;
        // 获取停车场ID
        $service = new ParkingService($hotel_id);
        $data = [
            'pageNum' => $page,
            'pageSize' => $pagesize,
            'startTime' => $start_time,
            'endTime' => $end_time,
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getChargeInfo',$data);
        $res = json_decode($res,true);

        $resdatak = [
            'recordList' => [],
            'recordCount'=> 0,
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        if(empty($res['result']['recordList'])){
            return returnData(200, 1, $resdatak, 'ok');
        }
        $resdatak = [
            'recordList' => $res['result']['recordList'],
            'recordCount'=> $res['result']['recordCount'],
            'page' => $page,
            'pagesize' => $pagesize,
        ];
        return returnData(200, 1, $resdatak, 'ok');
    }

    // 获取临时车缴费金额
    public function getParkingCarFee(Request $request) {
          info($request->all());

        $sub_mch_id = $request->get('sub_mch_id');
        $carNo = $request->get('carNo');
        if(empty($sub_mch_id)){
            return returnData(205, 0, [], '商家 sub_mch_id 不能为空');
        }
        if(empty($carNo)){
            return returnData(205, 0, [], '车牌号码 不能为空');
        }
        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        if(empty($oauthinfo->hotel_id)){
            return returnData(205, 0, [], '此商家还没有入驻平台,请检查!');
        }
        $hotel_id = $oauthinfo->hotel_id;
        // 获取停车场ID
        $service = new ParkingService($hotel_id);
        $data = [
            'carNo' => $carNo,
        ];
        //$res = $service->sendapi('yunpark/thirdInterface/getCarFee',$data);
        //$res = json_decode($res,true);

        $res_json = '{"success":true,"message":"success","code":200,"timestamp":1659341416230,"result":{"parkingNo":"P1700624069382","carNo":"湘D88888","openId":"123456","carType":11,"chargeTime":"2022-08-01 14:31:19","endChargeTime":"2022-08-01 16:10:16","totalAmount":800,"disAmount":0,"couponAmount":800,"mac":"574bfaeb-d3a361a5"}}';
        $res = json_decode($res_json, true);

        if (empty($res['result']['carNo'])) {
            return returnData(204, 0, [], '未查询到车辆入场信息');
        }
        $res['result']['carNo'] = $carNo;
        return returnData(200, 1, $res['result'], 'ok');

    }

    // 临时车 免费放行
    public function carFreePut(Request $request){
        $sub_mch_id = $request->get('sub_mch_id');
        $carNo = $request->get('carNo');
        $room_no = $request->get('room_no');
        if(empty($sub_mch_id)){
            return returnData(205, 0, [], '商家 sub_mch_id 不能为空');
        }
        if(empty($carNo)){
            return returnData(205, 0, [], '车牌号码 不能为空');
        }
        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        if(empty($oauthinfo->hotel_id)){
            return returnData(205, 0, [], '此商家还没有入驻平台,请检查!');
        }

        $hotel_id = $oauthinfo->hotel_id;
        $service = new ParkingService($hotel_id);
        $res = true;//$service->carFreePut($carNo);
        if($res === false){
            return returnData(203, 0, [], '遇到错误,车辆免费放行失败');
        }
        return returnData(200, 1, [], '操作免费放行成功');
    }


}
