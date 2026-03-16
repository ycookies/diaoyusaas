<?php

namespace App\Services;

use App\Models\Hotel\Users as UsersModel;
use App\Models\Hotel\UsersInfo as UsersInfoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * 融宝支付服务接口
 * @package App\Services
 * anthor Fox
 */
class RongbaoPayService extends BaseService {
    protected  $host = 'https://interface.rongbaokeji.com/';
    protected  $token = 'fed394778666ffa0946fff597c8eb0e4';

    public function payNotice($data){

    }

    public function sendapi($apiname,$data){
        $data['token'] = $this->token;
        $res = HttpsCurl($this->host.$apiname,$data);
        return $res;
    }

    public function getOrderDetail($out_trade_no){
        $apiname = 'api/payment_min_pay/getOrderByTradeno';
        $data = [
            'outtradeno' => $out_trade_no,
        ];
        return $this->sendapi($apiname,$data);

    }

    public function getOrderByOpenID($openid,$sub_mch_id,$page = 1,$pagesize = 10){
        $apiname = 'api/payment_min_pay/getOrderByOpenID';
        $data = [
            'openid' => $openid,
            'sub_mch_id' => $sub_mch_id,
            'page'=> $page,
            'pagesize' => $pagesize,
            'source' => 'payisv',
        ];
        return $this->sendapi($apiname,$data);
    }

}