<?php

namespace App\Services;

use App\Models\Hotel\Invoicerecord as record;
use App\Models\Hotel\InvoiceRegister;

/**
 * 发票服务
 * @package App\Services
 * anthor Fox
 */
class InvoiceService extends BaseService {


    public function bookingRoom($hotel_id,$invoice_amount, $Invoicerecord) {
        /*$hotel_id       = $order->hotel_id;
        $invoice_amount = $order->total_cost;*/
        $service        = new NuonuoService();
        $invoice_record = $Invoicerecord->toArray();
        $service->setHotel($hotel_id);
        $orderNo             = $invoice_record['orderNo'];//$Invoicerecord->orderNo; //;;date('YmdHis') . rand(1000, 9999);
        $seller_Invoice_info = InvoiceRegister::where(['hotel_id' => $hotel_id])->first();
        if (!$seller_Invoice_info) {
            return false;
        }
        $invoice_order = [
            /*'buyerName'       => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
            'buyerTaxNum'     => '91440300582733406C', // 购方税号（企业要填
            'buyerAccount'    => '中国工商银行 111111111111',
            'buyerAddress'    => '广东省深圳市龙华区下梅林尚书苑1栋209室',
            'buyerPhone'      => '17681849188', // 推送手机
            'email'           => '124355733@qq.com', // 推送邮箱*/
            'pushMode'        => '2', // 推送方式：-1,不推送;0,邮箱;1,手机（默认）;2,邮箱、手机
            'salerTaxNum'     => $seller_Invoice_info->salerTaxNum,// 销方税号（使用沙箱环境请求时消息体参数salerTaxNum和消息头参数userTax填写339902999999789113）
            'salerTel'        => $seller_Invoice_info->salerTel,
            'salerAddress'    => $seller_Invoice_info->salerAddress,// 销方地址,
            'orderNo'         => $orderNo, // 订单号（每个企业唯一） 20
            'invoiceDate'     => date('Y-m-d H:i:s'), // 订单时间  2022-01-13 12:30:00
            'clerk'           => '李想', // 开票员,
            'invoiceType'     => '1', // 开票类型：1:蓝票;2:红票
            'extensionNumber' => '923',  // 分机号 923 可以开数电票 888 可开普票、专票等发票
            'invoiceLine'     => 'pc', // 发票种类： pc 数电普票 ;bs:电子发票(增值税专用发票)-即数电专票(电子)
            'Callbackurl'     => $service->callbackurl,
            'invoiceDetail'   => [
                [
                    'goodsName'   => '酒店住宿费', // 商品名称
                    'withTaxFlag' => '1',// 单价含税标志：0:不含税,1:含税
                    'taxRate'     => '0.13',
                    'price'       => $invoice_amount,
                    'num'         => '1',
                    'unit'        => '天',
                    //'invoiceLineProperty' => 0 ,// 发票行性质：0,正常行;1,折扣行;2,被折扣行，红票只有正常行
                ],
            ]
        ];
        if (!empty($invoice_record['buyerName'])) {
            $invoice_order['buyerName'] = $invoice_record['buyerName'];
        }
        if (!empty($invoice_record['buyerTaxNum'])) {
            $invoice_order['buyerTaxNum'] = $invoice_record['buyerTaxNum'];
        }
        if (!empty($invoice_record['buyerBankAccount'])) {
            $invoice_order['buyerAccount'] = $invoice_record['buyerBankName'] . ' ' . $invoice_record['buyerBankAccount'];
        }
        if (!empty($invoice_record['buyerAddress'])) {
            $invoice_order['buyerAddress'] = $invoice_record['buyerAddress'];
        }
        if (!empty($invoice_record['buyerPhone'])) {
            $invoice_order['buyerPhone'] = $invoice_record['buyerPhone'];
        }
        if (!empty($user_invoice_title['buyerEmail'])) {
            $invoice_order['email'] = $invoice_record['buyerEmail'];
        }

        $postdata = [
            'order' => $invoice_order
        ];

        $resstr = $service->sendApiPost('nuonuo.OpeMplatform.requestBillingNew', $postdata);

        $res    = json_decode($resstr, true);
        // 申请开具发票成功
        /**
         * {
         * "code": "E0000",
         * "describe": "开票提交成功",
         * "result": {
         * "invoiceSerialNum": "24052218155903719044"
         * }
         * }
         */
        if (!empty($res['code']) && $res['code'] == 'E0000') {
            if (!empty($res['result']['invoiceSerialNum'])) { // 发票流水号
                $updata = [
                    'invoiceSerialNum' => $res['result']['invoiceSerialNum'],
                    'status'           => 1,
                ];
                record::where(['id' => $invoice_record['id']])->update($updata);
            }
        } else {
            $updata = [
                'status' => 3,//开票失败
            ];
            record::where(['id' => $invoice_record['id']])->update($updata);
        }
        return false;
    }
}