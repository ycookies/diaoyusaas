<?php
return [
    'labels'  => [
        'OrderDetail'  => '订单详情',
        'order-detail' => '订单详情',
    ],
    'fields'  => [
        'id'                    => 'id',
        'order_id'              => '订单ID',
        'goods_id'              => '商品ID',
        'num'                   => '购买商品数量',
        'unit_price'            => '商品单价',
        'total_original_price'  => '商品原总价(优惠前)',
        'total_price'           => '商品总价(优惠后)',
        'member_discount_price' => '会员优惠金额(正数表示优惠，负数表示加价)',
        'goods_info'            => '购买商品信息',
        'is_delete'             => '是否删除',
        'is_refund'             => '是否退款',
        'refund_status'         => '售后状态 0--未售后 1--售后中 2--售后结束',
        'back_price'            => '后台优惠(正数表示优惠，负数表示加价)',
        'sign'                  => '订单详情标识，用于区分插件',
        'goods_no'              => '商品货号',
        'form_data'             => '自定义表单提交的数据',
        'form_id'               => '自定义表单的id',
    ],
    'options' => [
    ],
];
