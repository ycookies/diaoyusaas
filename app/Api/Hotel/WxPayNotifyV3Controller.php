<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\HotelDevice;
use App\Models\Hotel\MemberOrder;
use App\Models\Hotel\MinpayAsynNotify;
use App\Models\Hotel\ParkingOrder;
use App\Models\Hotel\RechargeOrder;
use App\Models\Hotel\RoomBookingLog;
use App\Models\Hotel\Setting;
use App\Models\Hotel\TradeOrder;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use App\Models\Hotel\UserLevelUpOrder;
use App\Services\RongbaoPayService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;

// 微信小程序
class WxPayNotifyV3Controller extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    // 酒店小程序支付通知
    public function wxPayNotify(Request $request, $id) {
        info('V3-酒店小程序支付通知-总通知');
        //$app = app('wechat.isvpay')->make();
        //$this->config = WxappConfig::getToappidConfig($id);
        //$app          = Factory::payment($this->config);

        // 你的逻辑
        $message = $this->handlePaidNotify();
        if($message === false){
            info('V3-酒店小程序支付通知-解密失败');
        }
        addlogs('wxPayNotify', [], $message);
        if ($message['trade_state'] == 'SUCCESS') {
            $out_trade_no = $message['out_trade_no'];
            // 订房订单
            if (substr($out_trade_no, 0, 2) == '11') {
                $this->bookingOrder($message);
            }
            // 购买付费会员
            if (substr($out_trade_no, 0, 3) == 'VIP') {
                info('购买超级vip会员');
                $this->buyVipMember($message);
            }

            // 付费提升会员等级
            if (substr($out_trade_no, 0, 2) == 'LV') {
                info('付费提升会员等级');
                $this->buyUserLevel($message);
            }

            // 储值卡充值
            if (substr($out_trade_no, 0, 2) == 'RE') {
                info('储值卡充值');
                $this->rechargeHandle($message);
            }

            // 停车缴费
            if (substr($out_trade_no, 0, 4) == 'PARK') {
                info('停车缴费');
                $this->parkingHandle($message);
            }

            // 商家收款
            if (substr($out_trade_no, 0, 2) == 'TR') {
                info('商家收款');
                $this->tradeHandle($message);
            }

            // 当面付收款
            if (substr($out_trade_no, 0, 2) == 'TM') {
                info('当面付收款');
                $this->tradePayHandle($message);
            }
            // 活动报名缴费
            if (substr($out_trade_no, 0, 2) == 'HD') {
                info('活动报名缴费');
                $this->huodongOrder($message);
            }

            // 团购服务商品
            if (substr($out_trade_no, 0, 2) == '21') {
                info('V3-团购服务商品');
                $this->tuangouOrder($message);
            }

        }
        info('V3-小程序支付通知');
        return response()->json([
            'code'    => 'SUCCESS',
            'message' => '签名验证成功',
            'data'    => [],
        ]);
    }

    public function handlePaidNotify() {
        $errmsg = '';
        try {
            // 获取支付服务商 配置
            $request              = request();
            $config               = config('wechat.min2');
            $inBody               = file_get_contents('php://input');
            $inWechatpaySignature = $request->header('wechatpay-signature');// 请根据实际情况获取
            $inWechatpayTimestamp = $request->header('wechatpay-timestamp');// 请根据实际情况获取
            $inWechatpayNonce     = $request->header('wechatpay-nonce');// 请根据实际情况获取

            // 构造验签名串
            $platformPublicKeyMap = $config['platform_certs']; // 获取验签证书列表
            // 从回调通知请求头上获取声明的 平台公钥实例标识
            $inWechatpaySerial = $request->header('wechatpay-serial');

            // 判断预先已知的 `$platformPublicKeyMap` 是否存在，不存在则按照开发文档响应非20x状态码及负载JSON
            if (!\array_key_exists($inWechatpaySerial, $platformPublicKeyMap)) {
                info('未知的wechatpay-serial请求头');
                throw new \UnexpectedValueException('未知的wechatpay-serial请求头');
            }
            // 加载平台公钥实例
            $platformCertsFileFilePath = 'file://' . $platformPublicKeyMap[$inWechatpaySerial]; // 证书文件路径
            $platformPublicKeyInstance = Rsa::from($platformCertsFileFilePath, Rsa::KEY_TYPE_PUBLIC);

            info('加密体-公钥实例', [$inBody, $request->header(), $platformCertsFileFilePath]);
            // 验证签名
            // 检查通知时间偏移量，允许5分钟之内的偏移
            $timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int)$inWechatpayTimestamp);
            $verifiedStatus   = Rsa::verify(
                Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
                $inWechatpaySignature,
                $platformPublicKeyInstance
            );

            if ($verifiedStatus) {
                // 转换通知的JSON文本消息为PHP Array数组
                $inBodyArray = (array)json_decode($inBody, true);
                $ciphertext  = !empty($inBodyArray['resource']['ciphertext']) ? $inBodyArray['resource']['ciphertext'] : '';
                $nonce       = !empty($inBodyArray['resource']['nonce']) ? $inBodyArray['resource']['nonce'] : '';
                $aad         = !empty($inBodyArray['resource']['associated_data']) ? $inBodyArray['resource']['associated_data'] : '';
                //
                $apivKey = $config['v3_secret_key'];
                if (strpos($inWechatpaySerial, 'PUB_KEY_ID_') !== false) {
                    info('使用的是微信公钥验签');
                }
                // 加密文本消息解密
                info('加密文本消息解密', [$ciphertext, $apivKey, $nonce, $aad]);
                $inBodyResource = AesGcm::decrypt($ciphertext, $apivKey, $nonce, $aad);
                // 把解密后的文本转换为PHP Array数组
                $inBodyResourceArray = json_decode($inBodyResource, true);

                info('V3-支付通知解密数据:', $inBodyResourceArray);

                return $inBodyResourceArray;
            }
        } catch (\Error $error) {
            $errmsg = $error->getMessage();
        } catch (\Exception $exception) {
            $errmsg = $exception->getMessage();
        }
        info('V3-支付通知错误异常:'.$errmsg);
        return false;

    }

    // 客房预订处理
    public function bookingOrder($message) {
        $out_trade_no  = $message['out_trade_no'];
        $date          = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate = date_format($date, 'Y-m-d H:i:s');
        $orderinfo     = BookingOrder::with('room', 'hotel')->where(['out_trade_no' => $out_trade_no])->first();
        if ($orderinfo->status != 2) {
            $updata = [
                'status'     => 2,
                'pay_status' => 1,
                'trade_no'   => $message['transaction_id'],
                'pay_time'   => $formattedDate,
            ];
            BookingOrder::where(['out_trade_no' => $out_trade_no])->update($updata);
            // 增加订房记录，更新房态
            RoomBookingLog::addlog($out_trade_no);

            // 给用户增加订房次数
            \App\Models\Hotel\User::addBookingNum($orderinfo->user_id);
        }
        // 推送交易通知内容
        $this->sendRongbaoPayNotify($message, $orderinfo);

        // 驱动订单接单提醒
        $this->gzhTplMsgBookingOrder($message, $orderinfo);

        return false;
    }

    // 购买vip处理
    private function buyVipMember($message) {
        $viporder = MemberOrder::where(['order_no' => $message['out_trade_no']])->first();
        // 更新订单
        $date          = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate = $date->format('Y-m-d H:i:s');
        $updata        = [
            'trade_no'   => $message['transaction_id'],
            'pay_status' => 1,
            'pay_time'   => $formattedDate,
        ];
        MemberOrder::where(['order_no' => $message['out_trade_no']])->update($updata);

        // 更新用户信息
        \App\User::upVip($viporder->user_id, $viporder);

        return true;
    }

    // 付费提升会员等级
    private function buyUserLevel($message) {
        $level_up_order = UserLevelUpOrder::where(['order_no' => $message['out_trade_no']])->first();
        if (!$level_up_order) {
            // 找不到订单信息
            info('找不到订单信息', $message);
            return false;
        }
        // 更新订单
        $date          = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate = $date->format('Y-m-d H:i:s');
        $updata        = [
            'trade_no'   => $message['transaction_id'],
            'pay_status' => 1,
            'pay_time'   => $formattedDate,
        ];
        UserLevelUpOrder::where(['order_no' => $message['out_trade_no']])->update($updata);

        // 更新用户信息
        \App\User::uplevel($level_up_order->user_id, $level_up_order->level_id, '');

        /*$res = \App\User::where(['id' => $level_up_order->user_id])->update([
            'level_id'     => $level_up_order->level_id,
            'user_level' => $level_up_order->level_num,
        ]);*/

        // 发送短信 通知用户

        return true;
    }

    // 储值卡充值
    private function rechargeHandle($message) {

        $orderinfo = RechargeOrder::where(['order_no' => $message['out_trade_no']])->first();
        if (!empty($orderinfo->trade_no)) {
            return false;
        }
        $orderinfo->trade_no   = $message['transaction_id'];
        $orderinfo->pay_status = 1;
        $orderinfo->save();

        $total_fee = $message['total_fee'] * 0.01;

        $full_price   = $total_fee + $orderinfo->give_price;
        $user_balance = \App\User::where(['id' => $orderinfo->user_id])->value('balance');
        // 添加余额变动日志
        $insdata = [
            'user_id'     => $orderinfo->user_id,
            'hotel_id'    => $orderinfo->hotel_id,
            'type'        => 1,
            'money'       => $full_price,
            'total_money' => bcadd($user_balance + $full_price, 2),
            'desc'        => '充值-微信',
        ];
        if (!empty($orderinfo->give_price)) {
            $insdata['custom_desc'] = '充值套餐赠送:' . $orderinfo->give_price;
        }
        //BalanceLog::addlog($insdata);

        // 更新用户信息
        \App\User::addBalance($orderinfo->user_id, $full_price, '充值-微信');

        // 奖励积分
        \App\User::addPoint($orderinfo->user_id, 1, '充值-微信');
        return true;
    }

    // 发布公众号 模板消息
    public function gzhTplMsgBookingOrder($message, $orderinfo) {
        $app            = app('wechat.official_account');
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_notify_gzh_open_id'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_notify_gzh_open_id'])) {
            $openid          = $hotel_settings['booking_notify_gzh_open_id'];
            $settings        = Setting::getlists(['gzh_template']);
            $gzh_template_id = '';
            // 获取公众号 模板ID
            if (!empty($settings)) {
                foreach ($settings['gzh_template'] as $key => $items) {
                    if (!empty($items['gzh_template_key']) && trim($items['gzh_template_key']) == 'booking_success_notify_seller') {
                        $gzh_template_id = $items['gzh_template_id'];
                        break;
                    }
                }
            }
            if (!empty($openid && !empty($gzh_template_id))) {
                $tpl_data = [
                    'thing1'  => $orderinfo->room_type,
                    'time2'   => convertDateRange($orderinfo->arrival_time, $orderinfo->departure_time),
                    'thing3'  => $orderinfo->booking_name,
                    'thing4'  => $orderinfo->booking_phone,
                    'amount5' => $orderinfo->total_cost,
                ];
                $result   = $app->template_message->send([
                    'touser'      => $openid,
                    'template_id' => $gzh_template_id,
                    'url'         => env('APP_URL') . '/run/home?openid=' . $openid,
                    'data'        => $tpl_data,
                ]);
            } else {
                WxRobotError('订房公众号通知出错', '酒店:' . $orderinfo->hotel->name . ',订单号:' . $orderinfo->out_trade_no);
            }

            return true;
        }
        return false;
    }

    // 转发团购交易通知到 融宝支付
    public function sendTuangouRongbaoPayNotify($message, $orderinfo) {
        $out_trade_no = $orderinfo->order_no;
        $info         = MinpayAsynNotify::where(['order_no' => $orderinfo->out_trade_no, 'status' => 1])->first();
        // 检查是否已经发送成功过
        if (!empty($info->id)) {
            return false;
        }
        $sn_code     = HotelDevice::where(['hotel_id' => $orderinfo->hotel_id, 'device_type' => 'pos机'])->value('device_code');

        // 获取所有核销码
        $hexiao_code = \App\Models\Hotel\TicketsCode::getOrderAllHexiaoCode($orderinfo->order_no);

        $goods_info = $orderinfo->detail->goods_info;
        $remarks     = '团购线下服务: \n';
        $remarks     .= '商品标题:' . $goods_info['warehouse']['goods_name'] . ', \n';
        $remarks     .= '购买数量:' . $orderinfo->detail->num . ', \n';
        $remarks     .= '核 销 码:' . $hexiao_code . ' \n';

        $notice_data = $message;
        $notice_data['hexiao_code'] = $hexiao_code;
        $notice_data['remarks']     = $remarks;
        $notice_data['sn_code']     = $sn_code;
        $notice_data['order_tag']   = 'RBWX';
        $service                    = new RongbaoPayService();
        $res                        = $service->sendapi('api/payment_min_pay/wxNotifyUrlPayCode', $notice_data);
        //
        if(!is_array($res)){
            $res = json_decode($res,true);
            info('转发融宝支付',$res);
        }
        $res_str                    = $res;
        $res_arr                    = $res;

        $insdata = [
            'hotel_id'  => $orderinfo->hotel_id,
            'order_no'  => $out_trade_no,
            'send_data' => json_encode($notice_data, JSON_UNESCAPED_UNICODE),
            'resp_data' => $res_str,
            'status'    => 1,
        ];
        info('团购线下服务转发订单数据',[$notice_data,$res_arr]);
        if (!empty($res_arr['code']) && $res_arr['code'] == 1) {

        } else {
            if (!empty($res_arr['msg']) && $res_arr['msg'] == '订单号已存在') {
                $insdata['status'] = 1;
            } else {
                WxRobotError('推送支付信息出错', '订单号：' . $out_trade_no);
                $insdata['status'] = 0;
            }
            MinpayAsynNotify::create($insdata);

        }
        return true;
    }

    // 转发交易通知到 融宝支付
    public function sendRongbaoPayNotify($message, $orderinfo) {
        $out_trade_no = $orderinfo->out_trade_no;
        $info         = MinpayAsynNotify::where(['order_no' => $orderinfo->out_trade_no, 'status' => 1])->first();
        // 检查是否已经发送成功过
        if (!empty($info->id)) {
            return false;
        }
        $sn_code     = HotelDevice::where(['hotel_id' => $orderinfo->hotel_id, 'device_type' => 'pos机'])->value('device_code');
        $notice_data = $message;
        $remarks     = '微信小程序订房: \n';
        $remarks     .= '预订房型:' . $orderinfo->room_type . ', \n';
        $remarks     .= '预订天数:' . $orderinfo->days . ' 天, \n';
        $remarks     .= '住店离店:' . $orderinfo->arrival_time . ' -> ' . $orderinfo->departure_time . ', \n';
        $remarks     .= '预 订 人:' . $orderinfo->booking_name . ',\n ';
        $remarks     .= '预订电话:' . $orderinfo->booking_phone . ',\n';
        $remarks     .= '核 对 码:' . $orderinfo->code . ' \n';

        $booking_msg                = '预订酒店:' . $orderinfo->hotel->name . ',预订人:' . $orderinfo->booking_name . '，联系电话:' . $orderinfo->booking_phone;
        $notice_data['hexiao_code'] = $orderinfo->code;
        $notice_data['remarks']     = $remarks;
        $notice_data['sn_code']     = $sn_code;
        $notice_data['order_tag']   = 'RBWX';
        $service                    = new RongbaoPayService();
        $res                        = $service->sendapi('api/payment_min_pay/wxNotifyUrlPayCode', $notice_data);
        $res_arr                    = $res;
        $res_str                    = $res;
        if (!is_array($res_arr)) {
            $res_arr = json_decode($res, true);
        }
        if (is_array($res)) {
            $res_str = json_encode($res_str, JSON_UNESCAPED_UNICODE);
        }
        $insdata = [
            'hotel_id'  => $orderinfo->hotel_id,
            'order_no'  => $out_trade_no,
            'send_data' => json_encode($notice_data, JSON_UNESCAPED_UNICODE),
            'resp_data' => $res_str,
            'status'    => 1,
        ];
        if (!empty($res_arr['code']) && $res_arr['code'] == 1) {
            // 把订房通知发到企业微信群
            WxRobotBooking('有人订房', $booking_msg, $out_trade_no);
            $insdata['status'] = 1;
            MinpayAsynNotify::create($insdata);
        } else {
            if (!empty($res_arr['msg']) && $res_arr['msg'] == '订单号已存在') {
                $insdata['status'] = 1;
            } else {
                WxRobotError('推送支付信息出错', '订单号：' . $out_trade_no);
                $insdata['status'] = 0;
            }
            MinpayAsynNotify::create($insdata);

        }
        return true;
    }

    // 旅忆行 微信小程序支付通知
    public function wxPayNotifyWx8c3d9b0bbf9272bc(Request $request) {
        // 创建一个 DateTime 对象
        $config   = config('wechat.min1');
        $app      = Factory::payment($config);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 你的逻辑
            if ($message['result_code'] == 'SUCCESS') {
                $out_trade_no = $message['out_trade_no'];
                // 订房订单
                if (strpos($out_trade_no, 'YXB') !== false) {
                    $date          = \DateTime::createFromFormat('YmdHis', $message['time_end']);
                    $formattedDate = date_format($date, 'Y-m-d H:i:s');
                    $orderinfo     = BookingOrder::where(['out_trade_no' => $out_trade_no])->first();
                    if ($orderinfo->status != 2) {
                        info('更新订单');
                        $updata = [
                            'status'   => 2,
                            'trade_no' => $message['transaction_id'],
                            'pay_time' => $formattedDate,
                        ];
                        BookingOrder::where(['out_trade_no' => $out_trade_no])->update($updata);
                    }
                    // 驱动订单接单提醒
                }

            }
            info('小程序支付通知');
            info($message);
            return true; // 返回处理完成
        });
        return $response->send();
    }

    // 停车缴费处理
    public function parkingHandle($message) {
        $orderinfo = ParkingOrder::where(['outTradeNo' => $message['out_trade_no']])->first();
        if (empty($orderinfo->outTradeNo)) {
            return false;
        }
        $date                      = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate             = date_format($date, 'Y-m-d H:i:s');
        $orderinfo->transaction_id = $message['transaction_id'];
        $orderinfo->pay_time       = $formattedDate;
        $orderinfo->pay_status     = 1;
        $orderinfo->save();

        // 给车辆放行
        //$status = (new \App\Services\ParkingService($orderinfo->hotel_id))->paySuccess($message['out_trade_no']);
        $status = false;
        if ($status === true) {
            // 奖励积分
            // \App\User::addPoint($orderinfo->user_id, 1, '停车缴费');
        }
        return true;
    }

    // 线上普通收款
    public function tradeHandle($message) {
        $orderinfo = TradeOrder::where(['out_trade_no' => $message['out_trade_no']])->first();
        if (empty($orderinfo->out_trade_no)) {
            return false;
        }
        $date                  = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate         = date_format($date, 'Y-m-d H:i:s');
        $orderinfo->trade_no   = $message['transaction_id'];
        $orderinfo->pay_time   = $formattedDate;
        $orderinfo->pay_status = 1;
        $orderinfo->save();

        // 触发云打印


        $is_trade_reward_point = false;
        if ($is_trade_reward_point === true) {
            // 奖励积分
            // \App\User::addPoint($orderinfo->user_id, 1, '普通收款');
        }
        return true;
    }

    // 当面付收款
    public function tradePayHandle($message) {
        info('当面付收款', $message);
        $orderinfo = TradeOrder::where(['out_trade_no' => $message['out_trade_no']])->first();
        if (empty($orderinfo->out_trade_no)) {
            return false;
        }
        $date                  = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate         = date_format($date, 'Y-m-d H:i:s');
        $orderinfo->trade_no   = $message['transaction_id'];
        $orderinfo->pay_time   = $formattedDate;
        $orderinfo->pay_status = 1;
        $orderinfo->save();

        // 触发云打印


        $is_trade_reward_point = false;
        if ($is_trade_reward_point === true) {

        }
        return true;
    }

    // 活动报名订单更新
    public function huodongOrder($message) {
        $orderinfo = \App\Models\Hotel\HuodongOrder::where(['order_no' => $message['out_trade_no']])->first();
        if (empty($orderinfo->order_no)) {
            return false;
        }
        $date                = \DateTime::createFromFormat('YmdHis', $message['time_end']);
        $formattedDate       = date_format($date, 'Y-m-d H:i:s');
        $orderinfo->trade_no = $message['transaction_id'];
        //$orderinfo->pay_time = $formattedDate;
        $orderinfo->pay_status = 1;
        $orderinfo->save();

        // 添加到参入活动人员名单
        $bm_info  = json_decode($orderinfo->bm_info, true);
        $bm_name  = !empty($bm_info['bm_name']) ? $bm_info['bm_name'] : '';
        $bm_phone = !empty($bm_info['bm_phone']) ? $bm_info['bm_phone'] : '';
        \App\Models\Hotel\HuodongUser::addbm($orderinfo->hotel_id, $orderinfo->hd_id, $orderinfo->user_id, $bm_name, $bm_phone);

        return true;
    }

    // 团购服务商品
    public function tuangouOrder($message) {
        $orderinfo = \App\Models\Hotel\Order\Order::with('detail')->where(['order_no' => $message['out_trade_no']])->first();
        if (empty($orderinfo->order_no)) {
            return false;
        }

        $dateTime = new \DateTime($message['success_time']);
        $dateTime->setTimezone(new \DateTimeZone('Asia/Shanghai')); // 设置为东八区
        $formattedDate =  $dateTime->format('Y-m-d H:i:s');
        $orderinfo->trade_no = $message['transaction_id'];
        $orderinfo->pay_time = $formattedDate;
        $orderinfo->is_pay = 1;
        $orderinfo->save();


        // 更新团购订单
        TuangouOrderRelation::upOrderStatus($orderinfo->id, TuangouOrderRelation::Order_status_2);

        // 生成核销码
        \App\Models\Hotel\TicketsCode::generateCode($message['out_trade_no']);

        // 推送交易通知内容
        $this->sendTuangouRongbaoPayNotify($message, $orderinfo);

        $data = [
            'order_id' => $orderinfo->id,
            'data'     => json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        \App\Models\Hotel\Order\OrderPayResult::create($data);
        return true;
    }
}
