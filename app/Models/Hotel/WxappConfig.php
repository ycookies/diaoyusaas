<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class WxappConfig extends HotelBaseModel
{
	
    protected $table = 'wxapp_config';
    public $timestamps = false;
    public $guarded = [];


    public static function getConfig($hotel_id){
        $config = [];
        $info = WxappConfig::where(['hotel_id'=> $hotel_id])->first();
        if(!empty($info->id)){
            $config = [
                'app_id' => $info->appid, // 微信小程序的app_id 不是公众号ID
                'secret' => $info->appsecret, // 微信小程序的secret 不是公众号secret
                'mch_id'     => '1566291601', // 小程序绑定微信支付服务商 商户号
                'sub_mch_id' => $info->mchid, // 子商户号
                'key'        => $info->apikey, // 小程序绑定微信支付商户号 密钥
                'cert_path'  => $info->cert_pem, // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
                'key_path'   => $info->key_pem,      // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
                'notify_url' => env('APP_URL').'/hotel/notify/wxPayNotify',     // 这个小程序支付对应的支付通知地址
                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file' => storage_path('logs/easywechat-dev.log'),
                ],
            ];
        }
        return $config;
    }

    public static function getToappidConfig($app_id){
        $config = [];
        $info = WxappConfig::where(['appid'=> $app_id])->first();
        if(!empty($info->id)){
            $config = [
                'app_id' => $info->appid, // 支付小程序的app_id 不是公众号ID
                'secret' => $info->appsecret, // 支付小程序的secret 不是公众号secret
                'mch_id'     => $info->mchid, // 小程序绑定微信支付商户号
                'key'        => $info->apikey, // 小程序绑定微信支付商户号 密钥
                'cert_path'  => '/cert/wxpay/cert2/apiclient_cert.pem', // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
                'key_path'   => '/cert/wxpay/cert2/apiclient_key.pem',      // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
                'notify_url' => env('APP_URL').'/hotel/notify/wxPayNotify',     // 这个小程序支付对应的支付通知地址
                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file' => storage_path('logs/easywechat-dev.log'),
                ],
            ];
        }
        return $config;
    }
}
