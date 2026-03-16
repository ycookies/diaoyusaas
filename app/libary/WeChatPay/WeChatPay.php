<?php
namespace App\libary\WeChatPay;

use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

/**
 * 封装 WeChatPay
 * anthor Fox
 */
class WeChatPay {
    public function makePay(){
        $config = config('wechat.min2');

        // 商户号
        $merchantId = $config['mch_id'];
        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyFilePath = 'file://' . $config['key_path'];
        $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);

        // 「商户API证书」的「证书序列号」
        $merchantCertificateSerial = $config['serial_no'];

        // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
/*        $platformCertificateFilePath = 'file://' . $config['platform_cert_path'];
        $platformPublicKeyInstance   = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);*/

        // 加载平台公钥实例
        /*$platformCertificateFilePath = 'file://' . $config['platform_pub_cert']; // 证书文件路径
        $platformPublicKeyInstance = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);*/


        // 从「微信支付平台证书」中获取「证书序列号」
        //$platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateFilePath);

        // 从本地文件中加载「微信支付公钥」，用来验证微信支付应答的签名
        $platformPublicKeyFilePath    = 'file://' . $config['platform_pub_cert'];
        $twoPlatformPublicKeyInstance = Rsa::from($platformPublicKeyFilePath, Rsa::KEY_TYPE_PUBLIC);

        // 「微信支付公钥」的「微信支付公钥ID」
        // 需要在 商户平台 -> 账户中心 -> API安全 查询
        $platformPublicKeyId = $config['platform_pub_id'];
        // 构造一个 APIv3 客户端实例
        $instance = Builder::factory([
            'mchid'      => $merchantId,
            'serial'     => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $platformPublicKeyId => $twoPlatformPublicKeyInstance,
            ],
        ]);

        return $instance;
    }
    public function jspayOrder(){

    }

    public function make($endpoint,$params = [], $method = 'post', array $options = [], $returnResponse = false){
        $this->config = WxappConfig::getConfig(143);

        $config = config('wechat.min2');

        // 商户号
        $merchantId = $config['mch_id'];
        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyFilePath = 'file://' . $config['key_path'];
        $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);

        // 「商户API证书」的「证书序列号」
        $merchantCertificateSerial = $config['serial_no'];

        // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
        $platformCertificateFilePath = 'file://' . $config['platform_cert_path'];
        $platformPublicKeyInstance   = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);

        // 从「微信支付平台证书」中获取「证书序列号」
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateFilePath);

        // 构造一个 APIv3 客户端实例
        $instance = Builder::factory([
            'mchid'      => $merchantId,
            'serial'     => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $platformCertificateSerial => $platformPublicKeyInstance,
            ],
        ]);

        // 发送请求
        /*$resp = $instance->chain('v3/certificates')->get(
            ['debug' => false] // 调试模式
        );*/

        // 开启 特约商户号 点金计划
        /*$resp = $instance->chain('/v3/goldplan/merchants/changegoldplanstatus')->post([
            'json' => [
                'sub_mchid'           => $this->config['sub_mch_id'],
                'operation_type'      => 'OPEN',
                'operation_pay_scene' => 'JSAPI_AND_MINIPROGRAM'
            ]
        ]);*/

        // 开启或关闭 特约商户号 商家小票功能
        $resp = $instance->chain($endpoint)->post([
            'json' => [
                'sub_mchid'           => $this->config['sub_mch_id'],
                'operation_type'      => 'OPEN',
            ]
        ]);

        $res = $resp->getBody()->getContents();
        return $res;
    }
}