<?php 
return [
    'labels' => [
        'SubscribeDeliveryOrder' => 'SubscribeDeliveryOrder',
        'subscribe-delivery-order' => 'SubscribeDeliveryOrder',
    ],
    'fields' => [
        'uid' => '下单人uid',
        'order_code' => '订单编号',
        'out_trade_no' => '预创建订单',
        'trade_no' => 'trade_no',
        'seller_id' => '商家id',
        'status' => '状态 \'1\' => ‘预约’, \'2\' => ‘完成’, \'3\' => ‘取消’,',
        'pickup_code' => '取货码',
        'refund_status' => '退款状态，0未退款,1退款中，2同意退款，3拒接退款',
        'receiving_way' => '收货方式，1.送货到房间，2前台自提',
        'room_number' => '房间号',
        'consignee_name' => '收货人姓名',
        'consignee_mobile' => '收货人手机号',
        'remark' => '备注',
        'complete_time' => '完成时间',
        'time' => 'time',
        'appointment_time' => '预约时间',
        'id_del' => '0未删除1已删除',
    ],
    'options' => [
    ],
];
