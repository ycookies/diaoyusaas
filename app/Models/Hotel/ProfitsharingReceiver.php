<?php

namespace App\Models\Hotel;


class ProfitsharingReceiver extends HotelBaseModel {
    const Type_arr = [
        'MERCHANT_ID'         => '商户',
        'PERSONAL_OPENID'     => '个人openid',
        'PERSONAL_SUB_OPENID' => '个人sub_openid',
    ];

    const Relation_type_arr = [
        'SERVICE_PROVIDER' => '服务商',
        'STORE'            => '门店',
        'STAFF'            => '员工',
        'STORE_OWNER'      => '店主',
        'PARTNER'          => '合作伙伴',
        'HEADQUARTER'      => '总部',
        'BRAND'            => '品牌方',
        'DISTRIBUTOR'      => '分销商',
        'USER'             => '用户',
        'SUPPLIER'         => '供应商',
    ];
    protected $table = 'hotel_profitsharing_receiver';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    // 添加一个接受人
    public static function add($data){
        return self::updateOrInsert(['hotel_id'=>$data['hotel_id'],'relation_type' => $data['relation_type']],$data);
    }

    // 给酒店分账添加服务商分账方
    public static function addIsvReceiverToPay($hotel_id){
        $info = self::where(['hotel_id'=>$hotel_id,'relation_type' => 'SERVICE_PROVIDER'])->first();
        // 如何已经有了，就不添加了
        if(!empty($info->is_receiver_pay)){
            return false;
        }
        $app = app('wechat.isvpay')->setSubMerchant($hotel_id);
        $receiver = [
            "type"          => "MERCHANT_ID",
            "account"       => "1566291601",//PERSONAL_OPENID：个人openid
            "name"          => "融宝服务商",//接收方真实姓名
            "relation_type" => "SERVICE_PROVIDER"
        ];
        $mk           = $app->profit_sharing->addReceiver($receiver);
        if(empty($mk['sub_mchid'])){
            return '添加服务商分账接受方失败';
        }
        return true;
    }

    //  计算分账总支出金额
    public static function jisuanProfitsharingOutMoney($order_price,$receiver_list){
        $receiver_money_arr = [];
        $total_money = 0;
        foreach ($receiver_list as $key => $item) {
            $money = round($order_price * ($item->rate * 0.01),2);
            $receiver_money_arr[$item->account] = $money;
            $total_money = $total_money + $money;
        }

        return [
            'total_money' => $total_money,
            'receiver_money_list' => $receiver_money_arr
        ];

    }
}
