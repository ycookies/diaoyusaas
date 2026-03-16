<?php

namespace App\libary\Alisms;

use Overtrue\EasySms\EasySms;
/**
 * 阿里云短信发送
 */
class Alisms {
    //
    public function make(){
        $formdata     = \App\Models\Hotel\Setting::getlists(['sms_aliyun_key', 'sms_aliyun_secret','sms_aliyun_sign']);
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                /*'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],*/
                'aliyun' => [
                    'access_key_id' => $formdata['sms_aliyun_key'],
                    'access_key_secret' => $formdata['sms_aliyun_secret'],
                    'sign_name' => $formdata['sms_aliyun_sign'],
                ],
            ],
        ];

        return new EasySms($config);
    }
}
