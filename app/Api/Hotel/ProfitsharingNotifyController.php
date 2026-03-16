<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\ProfitsharingOrder;
use App\Services\BookingOrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;

// 分账动账通知
class ProfitsharingNotifyController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    // 分账动账通知
    public function notify(Request $request) {
        info('分账动账通知');
        info('分账动账通知请求头:', $request->header());
        info('分账动账通知请求内容:', $request->all());
        // 获取支付服务商 配置
        $config = config('wechat.min2');
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
            $ciphertext = !empty($inBodyArray['resource']['ciphertext']) ? $inBodyArray['resource']['ciphertext']:'';
            $nonce      = !empty($inBodyArray['resource']['nonce']) ? $inBodyArray['resource']['nonce']:'';
            $aad        = !empty($inBodyArray['resource']['associated_data']) ? $inBodyArray['resource']['associated_data']:'';
            //
            $apivKey = $config['v3_secret_key'];
            if(strpos($inWechatpaySerial,'PUB_KEY_ID_') !== false){
                info('使用的是微信公钥验签');
            }
            // 加密文本消息解密
            info('加密文本消息解密',[$ciphertext, $apivKey, $nonce, $aad]);
            $inBodyResource = AesGcm::decrypt($ciphertext, $apivKey, $nonce, $aad);
            // 把解密后的文本转换为PHP Array数组
            $inBodyResourceArray = json_decode($inBodyResource, true);

            info('分账动账通知解密数据:', $inBodyResourceArray);

            // 更新分账状态
            if(!empty($inBodyResourceArray['out_order_no'])){
                $service = new \App\Services\ProfitsharingService();
                $service->profitsharingQuery($inBodyResourceArray['out_order_no']);
            }
            return response()->json([
                'code'    => 'SUCCESS',
                'message' => '签名验证成功',
                'data'    => [],
            ]);
        }

        info('分账动账通知解密数据 - 签名验证失败');
        return response()->json([
            'code'    => 'FAIL',
            'message' => '签名验证失败',
            'data'    => [],
        ]);

    }

    // 检查全部分账结果
    public function queryFullProfitsharing() {
        $service = new BookingOrderService();
        $lists   = ProfitsharingOrder::where(['profitsharing_status' => 'PROCESSING'])->get();
        foreach ($lists as $key => $items) {
            $res = $service->profitsharingQuery($items->profitsharing_no);
        }
        return 'ok';
    }

}
