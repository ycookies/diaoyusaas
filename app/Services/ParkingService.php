<?php

namespace App\Services;

use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\ParkingOrder;
use Dcat\Admin\Admin;

/**
 * 停车场服务
 * @package App\Services
 * anthor Fox
 */
class ParkingService extends BaseService {
    protected $host = 'https://parking.yszbyun.cn/';
    public $parkingNo;

    public function __construct($hotel_id) {
        $flds            = [
            'parkingNo',
        ];
        $formdata        = HotelSetting::getlists($flds, $hotel_id);
        $parkingNo       = !empty($formdata['parkingNo']) ? $formdata['parkingNo'] : '';
        $this->parkingNo = $parkingNo;
        parent::__construct('');
    }

    // 获取停车场编号
    public function getParkingNo() {
        return $this->parkingNo;
    }

    public function sendapi($apiname, $data) {
        $data['parkingNo'] = $this->parkingNo;
        $host              = $this->host . $apiname;
        $res               = HttpsCurl($host, $data);
        addlogs($apiname, $data, $res);
        return $res;
    }

    // 支付完成，给车辆放行
    public function paySuccess($outTradeNo) {
        $orderinfo = ParkingOrder::where(['outTradeNo' => $outTradeNo])->first();
        $postdata  = [
            'carNo'         => $orderinfo->carNo,
            "chargeTime"    => $orderinfo->chargeTime,
            "couponAmount"  => $orderinfo->couponAmount,
            "disAmount"     => $orderinfo->disAmount,
            "endChargeTime" =>  $orderinfo->pay_time, // 支付完成时间
            "startTime"     =>  $orderinfo->chargeTime,
            "endTime"       =>  $orderinfo->endChargeTime,
            //"mac"           => "58a4d967-b50a4bdc",
            "openid"        => $orderinfo->openid,
            "outTradeNo"    => $outTradeNo,
            "parkingNo"     => $this->parkingNo,
            "payType"       => 1,
            "totalAmount"   => $orderinfo->totalAmount,
            'transactionId' => $orderinfo->transaction_id,
        ];
        return $postdata;
        $res       = $this->sendapi('yunpark/thirdInterface/paySuccess', $postdata);
        $res       = json_decode($res, true);
        if (!empty($res['code']) && $res['code'] == 200 && empty($res['message'])) {
            return true;
        }
        return false;
    }

    // 临时车给车辆放行
    public function carFreePut($carNo) {
        $postdata  = [
            'carNo'         => $carNo,
            "chargeTime"    => $orderinfo->chargeTime,
            "couponAmount"  => $orderinfo->couponAmount,
            "disAmount"     => $orderinfo->disAmount,
            "endChargeTime" =>  $orderinfo->pay_time, // 支付完成时间
            "startTime"     =>  $orderinfo->chargeTime,
            "endTime"       =>  $orderinfo->endChargeTime,
            //"mac"           => "58a4d967-b50a4bdc",
            "openid"        => $orderinfo->openid,
            "outTradeNo"    => $outTradeNo,
            "parkingNo"     => $this->parkingNo,
            "payType"       => 1,
            "totalAmount"   => $orderinfo->totalAmount,
        ];
        $res       = $this->sendapi('yunpark/thirdInterface/paySuccess', $postdata);
        $res       = json_decode($res, true);
        if (!empty($res['code']) && $res['code'] == 200 && empty($res['message'])) {
            return $res;
        }
        return false;
    }

    // 生成停车收费小程序码
    public static function makeParkingQrcode($hotel_id,$is_force = false){
        $filenamefull = 'parking-'.$hotel_id.'.png';
        $full_path = public_path('uploads/images').'/'.$filenamefull;
        $qrcode_url = env('APP_URL').'/uploads/images/'.$filenamefull;
        if(!$is_force){
            if(file_exists($full_path)){
                return $qrcode_url;
            }
        }
        $miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
        
        $response = $miniProgram->app_code->getQrCode('/pages2/extend/parking_pay_car_cost');
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            if(file_exists($full_path)){
                unlink($full_path);
            }
            $filename = $response->saveAs(public_path('uploads/images'), $filenamefull);
            if(!file_exists($full_path)){
                return returnData(204, 0, [], '保存小程序二维码失败');
            }
            $qrcode_url = env('APP_URL').'/uploads/images/'.$filenamefull;
            return $qrcode_url;
        }

        return false;
    }


}