<?php 
return [
    'labels' => [
        'DinnerOrderPayRecord' => 'DinnerOrderPayRecord',
        'dinner-order-pay-record' => 'DinnerOrderPayRecord',
    ],
    'fields' => [
        'order_id' => '订单编号',
        'hotel_id' => '商家id',
        'restaurant_id' => '餐厅id',
        'price' => '总价',
        'type' => '0支付1退款',
        'write_type' => '0手动添加1支付回调写入',
        'pay_status' => '1银行卡2支付宝3微信4现金',
        'refund_status' => '2支付宝原路4线下退回',
        'desk_number' => '桌号',
        'remark' => '备注',
        'time' => 'time',
        'status' => '0未完成1完成',
    ],
    'options' => [
    ],
];
