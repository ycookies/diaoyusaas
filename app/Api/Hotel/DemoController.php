<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\BookingOrder;
use App\Services\NuonuoService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;

// 测试
class DemoController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;
    public $openPlatform_obj;
    public $message_content;
    public $recharge_package = [
        [
            'id'        => 1,
            'name'      => '套餐0',
            'title'     => '0.1元',
            'sub_title' => '送0.01元',
            'cost'      => 0.1,
            'money'     => 0.11,
        ],
        [
            'id'        => 2,
            'name'      => '套餐1',
            'title'     => '1000元',
            'sub_title' => '送50元',
            'cost'      => 1000,
            'money'     => 1050,
        ],
        [
            'id'        => 3,
            'name'      => '套餐2',
            'title'     => '3000元',
            'sub_title' => '送100元',
            'cost'      => 3000,
            'money'     => 3100,
        ],
        [
            'id'        => 4,
            'name'      => '套餐3',
            'title'     => '5000元',
            'sub_title' => '送500元',
            'cost'      => 5000,
            'money'     => 5500,
        ]
    ];

    // 做测试的接口
    public function callbackTest(Request $request) {
        $message['out_trade_no'] = 'TG2025021210124123';
        //$orderinfo = \App\Models\Hotel\Order\Order::with('detail')->where(['order_no' => $message['out_trade_no']])->first();
        //$goods_info = $orderinfo->detail->goods_info;

        $all_code = \App\Models\Hotel\TicketsCode::getOrderAllHexiaoCode('TG2025021210124123');
        echo "<pre>";
        print_r($all_code);
        echo "</pre>";
        exit;
        echo "<pre>";
        print_r($goods_info['warehouse']['goods_name']);
        echo "</pre>";
        exit;
        /*$out_trade_no = '112024061622132593';
        $orderinfo    = BookingOrder::with('room', 'hotel', 'user')->where(['out_trade_no' => $out_trade_no])->first();
        return $this->userMinappMsgtplBookingFail($orderinfo);*/
        return '';
    }

    // 给用户发送通知 小程序
    public function userMinappMsgtplBookingSuccess($orderinfo) {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_success'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_minapp_msg_tpl_success']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_success'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                'data'        => [
                    'thing1'   => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'thing6' => [
                        'value' => $orderinfo->room_type, // 入住房型
                    ],
                    'date2' => [
                        'value' => $orderinfo->arrival_time, // 入住时间
                    ],
                    'date3' => [
                        'value' => $orderinfo->departure_time, // 离店时间
                    ],
                    'amount4' => [
                        'value' => $orderinfo->total_cost, // 订单金额
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingSuccess',$tpl_data,$result);
        }else{
            // 未能发送
        }
        return true;
    }

    // 给用户发送通知 小程序 取消
    public function userMinappMsgtplBookingCancel($orderinfo,$reason = '行程改变') {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_cancel'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_minapp_msg_tpl_cancel']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_cancel'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,

                'data'        => [
                    'thing1'   => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'character_string8' => [
                        'value' => $orderinfo->order_no, // 订单号
                    ],
                    'amount4' => [
                        'value' => $orderinfo->total_cost, // 订单金额
                    ],
                    'thing6' => [
                        'value' => $orderinfo->room_type, // 房型名称
                    ],
                    'thing7' => [
                        'value' => $reason, // 取消原因
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingSuccess',$tpl_data,$result);
        }else{
            // 未能发送
        }
        return returnData(200,1,$result,'ok');
    }

    // 给用户发送通知 小程序 失败
    public function userMinappMsgtplBookingFail($orderinfo,$reason = '已满房') {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_fail'], $orderinfo->hotel_id);
        $result = [];
        if (!empty($hotel_settings['booking_minapp_msg_tpl_fail']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_fail'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                'data'        => [
                    'thing1'   => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'character_string6' => [
                        'value' => $orderinfo->order_no, // 订单号
                    ],
                    'amount4' => [
                        'value' => $orderinfo->total_cost, // 租金总额
                    ],
                    'thing5' => [
                        'value' => $reason, // 失败原因
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingFail',$tpl_data,$result);
        }else{
            // 未能发送
        }
        return returnData(200,1,$result,'ok');
    }

    // 手动退款
    public function tuikuan(Request $request) {
        $out_trade_no = $request->get('out_trade_no', '');
        if (empty($out_trade_no)) {
            return returnData(204, 0, [], '请转入订单号');
        }
        $where[] = ['out_trade_no', '=', $out_trade_no];
        $detail  = BookingOrder::where($where)->first();

        /*$where[] = ['order_no', '=', $out_trade_no];
        $detail  = RechargeOrder::where($where)->first();*/

        if (!$detail) {
            return returnData(204, 0, [], '未找到订单号信息');
        }

        $hotel_id     = $detail->hotel_id;
        $refund_price = $detail->total_cost;

        //$refund_price = $detail->recharge_price;

        $isvpay         = app('wechat.isvpay');
        $app            = $isvpay->setSubMerchant($hotel_id);
        $out_request_no = 'R' . time();
        $refundFee      = bcmul($refund_price, 100, 0);

        $result = $app->refund->byTransactionId($detail->trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => '行程变动,取消预订',
        ]);
        addlogs('refund_byTransactionId', [$detail->trade_no, $out_request_no, $refundFee, $refundFee], $result);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {

            return returnData(200, 1, ['refund_id' => $result['refund_id']], '退款成功');
        }
        $emsg = !empty($result['return_msg']) ? $result['return_msg'] : '';
        return returnData(205, 0, $result, '退款失败：' . $emsg);
    }

    public function generateRandomNumber($length = 12) {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= random_int(0, 9);
        }
        return $number;
    }

    public function chongHongToPupiao() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'order' => [
                'buyerName'       => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
                'buyerTaxNum'     => '91440300582733406C', // 购方税号（企业要填
                'pushMode'        => '2', // 推送方式：-1,不推送;0,邮箱;1,手机（默认）;2,邮箱、手机
                'buyerPhone'      => '17681849188', // 推送手机
                'email'           => '124355733@qq.com', // 推送邮箱
                'salerTaxNum'     => '339901999999199',// 销方税号（使用沙箱环境请求时消息体参数salerTaxNum和消息头参数userTax填写339902999999789113）
                'salerTel'        => '0571-77777777',
                'salerAddress'    => '广东省深圳市福田区下梅林尚书苑1栋413室',// 销方地址,
                'orderNo'         => $orderNo, // 订单号（每个企业唯一） 20
                'invoiceDate'     => date('Y-m-d H:i:s'), // 订单时间  2022-01-13 12:30:00
                'clerk'           => '李想', // 开票员,
                'invoiceType'     => '2', // 开票类型：1:蓝票;2:红票
                'extensionNumber' => '888',  // 分机号 923 可以开数电票 888 可开普票、专票等发票
                'Callbackurl'     => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
                // 冲红选项
                'invoiceCode'     => '988074543614', //  冲红时填写的对应蓝票发票代码（红票必填 10位或12 位， 11位的时候请左补 0）
                'invoiceNum'      => '00001787', // 冲红时填写的对应蓝票发票号码（红票必填，不满8位请左补0）
                'redReason'       => '1', // 冲红原因：1:销货退回;2:开票有误;3:服务中止;4:发生销售折让(开具红票时且票种为p,c,e,f,r需要传--成品油发票除外；不传时默认为 1)
                'billInfoNo'      => '1403011904008400', // 红字信息表编号.专票冲红时此项必填，且必须在备注中注明“开具红字增值税专用发票信息表编号ZZZZZZZZZZZZZZZZ”字样，其 中“Z”为开具红字增值税专用发票所需要的长度为16位信息表编号（建议16位，最长可支持24位）。

                'invoiceDetail' => [
                    [
                        'goodsName'   => '酒店住宿费', // 商品名称
                        'withTaxFlag' => '1',// 单价含税标志：0:不含税,1:含税
                        'taxRate'     => '0.13',
                        'price'       => '444',
                        'num'         => '-1',
                        'unit'        => '天',
                        //'invoiceLineProperty' => 0 ,// 发票行性质：0,正常行;1,折扣行;2,被折扣行，红票只有正常行
                    ],
                ]
            ]
        ];
        // 开普电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.requestBillingNew', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 电子普票
    public function lanPiaoToPupiao() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'order' => [
                'buyerName'       => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
                'buyerTaxNum'     => '91440300582733406C', // 购方税号（企业要填
                'pushMode'        => '2', // 推送方式：-1,不推送;0,邮箱;1,手机（默认）;2,邮箱、手机
                'buyerPhone'      => '17681849188', // 推送手机
                'email'           => '124355733@qq.com', // 推送邮箱
                'salerTaxNum'     => '339901999999199',// 销方税号（使用沙箱环境请求时消息体参数salerTaxNum和消息头参数userTax填写339902999999789113）
                'salerTel'        => '0571-77777777',
                'salerAddress'    => '广东省深圳市福田区下梅林尚书苑1栋413室',// 销方地址,
                'orderNo'         => $orderNo, // 订单号（每个企业唯一） 20
                'invoiceDate'     => date('Y-m-d H:i:s'), // 订单时间  2022-01-13 12:30:00
                'clerk'           => '李想', // 开票员,
                'invoiceType'     => '1', // 开票类型：1:蓝票;2:红票
                'extensionNumber' => '888',  // 分机号 923 可以开数电票 888 可开普票、专票等发票
                'Callbackurl'     => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
                'invoiceDetail'   => [
                    [
                        'goodsName'   => '酒店住宿费', // 商品名称
                        'withTaxFlag' => '1',// 单价含税标志：0:不含税,1:含税
                        'taxRate'     => '0.13',
                        'price'       => '444',
                        'num'         => '1',
                        'unit'        => '天',
                        //'invoiceLineProperty' => 0 ,// 发票行性质：0,正常行;1,折扣行;2,被折扣行，红票只有正常行
                    ],
                ]
            ]
        ];
        // 开普电发票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.requestBillingNew', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 数电普票
    public function lanPiaoToShudian() {
        // 开普电发票
        $service = new NuonuoService();
        $service->setHotel(143);
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'order' => [
                'buyerName'       => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
                'buyerTaxNum'     => '91440300582733406C', // 购方税号（企业要填
                'buyerAccount'    => '中国工商银行 111111111111',
                'buyerAddress'    => '广东省深圳市龙华区下梅林尚书苑1栋209室',
                'pushMode'        => '2', // 推送方式：-1,不推送;0,邮箱;1,手机（默认）;2,邮箱、手机
                'buyerPhone'      => '17681849188', // 推送手机
                'email'           => '124355733@qq.com', // 推送邮箱
                'salerTaxNum'     => '339901999999199',// 销方税号（使用沙箱环境请求时消息体参数salerTaxNum和消息头参数userTax填写339902999999789113）
                'salerTel'        => '0571-77777777',
                'salerAddress'    => '广东省深圳市福田区下梅林尚书苑1栋413室',// 销方地址,
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
                        'price'       => '384',
                        'num'         => '1',
                        'unit'        => '天',
                        //'invoiceLineProperty' => 0 ,// 发票行性质：0,正常行;1,折扣行;2,被折扣行，红票只有正常行
                    ],
                ]
            ]
        ];

        $res = $service->sendApiPost('nuonuo.OpeMplatform.requestBillingNew', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    public function chongPiaoToShudian() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'order' => [
                'buyerName'       => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
                'buyerTaxNum'     => '91440300582733406C', // 购方税号（企业要填
                'pushMode'        => '2', // 推送方式：-1,不推送;0,邮箱;1,手机（默认）;2,邮箱、手机
                'buyerPhone'      => '17681849188', // 推送手机
                'email'           => '124355733@qq.com', // 推送邮箱
                'salerTaxNum'     => '339901999999199',// 销方税号（使用沙箱环境请求时消息体参数salerTaxNum和消息头参数userTax填写339902999999789113）
                'salerTel'        => '0571-77777777',
                'salerAddress'    => '广东省深圳市福田区下梅林尚书苑1栋413室',// 销方地址,
                'orderNo'         => $orderNo, // 订单号（每个企业唯一） 20
                'invoiceDate'     => date('Y-m-d H:i:s'), // 订单时间  2022-01-13 12:30:00
                'clerk'           => '李想', // 开票员,
                'invoiceType'     => '2', // 开票类型：1:蓝票;2:红票
                'extensionNumber' => '923',  // 分机号 923 可以开数电票 888 可开普票、专票等发票
                'invoiceLine'     => 'pc',
                'Callbackurl'     => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
                // 冲红选项
                //'invoiceCode'=> '20882405221205451836', //  冲红时填写的对应蓝票发票代码（红票必填 10位或12 位， 11位的时候请左补 0）
                //'invoiceNum'=> '20882405221205451836', // 冲红时填写的对应蓝票发票号码（红票必填，不满8位请左补0）
                'redReason'       => '1', // 冲红原因：1:销货退回;2:开票有误;3:服务中止;4:发生销售折让(开具红票时且票种为p,c,e,f,r需要传--成品油发票除外；不传时默认为 1)
                //'billInfoNo'=> '1403011904008410', // 红字信息表编号.专票冲红时此项必填，且必须在备注中注明“开具红字增值税专用发票信息表编号ZZZZZZZZZZZZZZZZ”字样，其 中“Z”为开具红字增值税专用发票所需要的长度为16位信息表编号（建议16位，最长可支持24位）。

                'invoiceDetail' => [
                    [
                        'goodsName'   => '酒店住宿费', // 商品名称
                        'withTaxFlag' => '1',// 单价含税标志：0:不含税,1:含税
                        'taxRate'     => '0.13',
                        'price'       => '366',
                        'num'         => '-1',
                        'unit'        => '天',
                        //'invoiceLineProperty' => 0 ,// 发票行性质：0,正常行;1,折扣行;2,被折扣行，红票只有正常行
                    ],
                ]
            ]
        ];
        // 开数电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.requestBillingNew', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 数电普票冲红
    public function chongPiaoToShudian2() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            //'billId' => $orderNo, // 红字确认单申请号，需要保持唯一，不传的话系统自动生成一个
            'blueInvoiceLine'   => 'pc', // 对应蓝票发票种类: bs:电子发票(增值税专用发票)， pc:电子发票(普通发票)
            'applySource'       => '0', // 申请方（录入方）身份： 0 销方 1 购方
            'blueInvoiceNumber' => '20882405221213481843', // 对应蓝字发票号码（蓝票是增值税发票时
            'sellerTaxNo'       => '339901999999199', // 销方税号
            'sellerName'        => '航信培训企业199', // 销方名称，申请说明为销方申请时可为空
            'buyerName'         => '深圳市融宝科技有限公司', // 购方名称 企业名称/个人
            'buyerTaxNo'        => '91440300582733406C',
            'redReason'         => '3', // 冲红原因： 1销货退回 2开票有误 3服务中止 4销售折让
            'extensionNumber'   => '923', // 分机号 923 可以开数电票 888 可开普票、专票等发票
            'blueDetailIndex'   => '1', // 对应蓝票明细行序号
            'goodsName'         => '酒店住宿费', // 商品名称
            'taxRate'           => '0.13',
            'goodsCode'         => '3070402000000000000',
            'num'               => '1',
            'unit'              => '天',
            'callbackUrl'       => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify'
        ];
        // 开数电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.saveInvoiceRedConfirm', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 数电普票冲红 诺税通saas红字确认单查询接口
    public function chongPiaoToShudian3() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'identity' => '0', //操作方身份： 0销方 1购方
            'billId'   => '1242818211788382208',
        ];
        // 开数电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.queryInvoiceRedConfirm', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 数电普票冲红 (诺税通saas红字确认单下载接口)
    public function chongPiaoToShudian4() {
        $service  = new NuonuoService();
        $orderNo  = date('YmdHis') . rand(1000, 9999);
        $postdata = [
            'identity'        => '0', //操作方身份： 0销方 1购方
            //'billUuid'   => '1242818211788382208',
            'startTime'       => '2024-05-20',
            'endTime'         => '2024-05-25',
            'extensionNumber' => '923'
        ];
        // 开数电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.refreshInvoiceRedConfirm', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }

    // 数电普票冲红 (诺税通saas红字确认单确认接口)
    public function chongPiaoToShudian5() {
        $service  = new NuonuoService();
        $postdata = [
            'identity'         => '1', //操作方身份： 0销方 1购方
            'extensionNumber'  => '923',
            'billId'           => '1242818211788382208',
            'confirmAgreement' => '1', // 处理意见： 0：拒绝 1：同意
            'confirmReason'    => '完全同意 ', // 处理理由
            'callbackUrl'      => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
        ];
        // 开数电红票
        $res = $service->sendApiPost('nuonuo.OpeMplatform.confirm', $postdata);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
    }
}
