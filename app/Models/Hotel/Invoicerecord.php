<?php

namespace App\Models\Hotel;


class Invoicerecord extends HotelBaseModel {

    // 1后台商户开票 2后台商户生成二维码(带商品信息) 3 后台统一二维码开票
    const Is_from_1 = 1;
    const Is_from_2 = 2;
    const Is_from_3 = 3;
    const Is_from_4 = 4;
    const Is_from_arr = [
        '1' => '后台商户开票',
        '2' => '后台商户生成二维码(带商品信息)',
        '3' => '后台统一二维码开票',
        '4' => '小程序用户自行开票',
    ];
    // 发票审核
    const Is_check_arr = [
        '1' => '需要审核',
        '2' => '不需要审核',
    ];
     // 消费开票类型
    const Fuwu_type_1 = 1;
    const Fuwu_type_2 = 2;
    const Fuwu_type_3 = 3;
    const Fuwu_type_4 = 4;
    const Fuwu_type_arr = [
        '1' => '酒店预订',
        '2' => '会员购买',
        '3' => '小超市',
        '4' => '积分商城实物',
    ];
    protected $table = 'invoicerecord';
    public $guarded = [];

    // 开票状态 success 开票成功 error 开票失败 wait 等待开票 wait_push 等待票宝通推送
    public static $status_arr = [
        'success'   => '开票成功',
        'error'     => '开票失败',
        'wait'      => '等待开票',
        'wait_push' => '等待票宝通推送',
    ];
}
