<?php

namespace App\Admin\Controllers;

use App\Merchant\Repositories\Room;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Room as RoomModel;
use EasyWeChat\Factory;
use App\Models\Hotel\WxappConfig;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

use WeChatPay\Crypto\AesGcm;
use WeChatPay\Formatter;

class CertController extends AdminController
{
    public $config;

    /**
     * 获取平台证书
     */
    public function index(Content $content)
    {
        self::certificates();
        exit;
    }

    public static function jiemi(){

        /*"DEefVajraX3qli1oSPOqGFM0oMoB0tRoeaJRpQhJ4RN0siQJVq9czbDemXN+t97WngSYTfnm2kgFECFV6NSje1Asy+XPA5Jei4m9RdQvFkX5v2H0NbXmRKZWgoF/93KopJzEzYLZ2E57aovqP5jaGyZ30Oc1hsUS6r7zyj08rMRdzhv+1jERHnoc1ci5UeRkFxJfCz8sY1Uwsn9FpUY9Q1lwhhpmJINvayzJENkp6DqaDCLvHPIEcBDB/QF1fEJLxN7i1f5Lp8LfJuSzvFHwgQceZDfcyGiblHoJKudpPpp7OJSWMvuPS1XD93GaUzcJLt1Dxjn2VCDqtSmBFCTZIvawGRn9NxdkCNAWWDIZZEQVVF0agX0MG/yjYm419eQx3JiXvZcbxKdXvLPID3YHDkqpx5w0fl0jUJbpY9ExmEeCXsHhQqTxmot3p3KzFg==",
"FbGgm3Y85Z7i0zmOZbxCfV4DTAPns6lO",
"ppuezvTya4eS",
"transaction"*/

        // 加密文本消息解密
        $ciphertext = "DEefVajraX3qli1oSPOqGFM0oMoB0tRoeaJRpQhJ4RN0siQJVq9czbDemXN+t97WngSYTfnm2kgFECFV6NSje1Asy+XPA5Jei4m9RdQvFkX5v2H0NbXmRKZWgoF/93KopJzEzYLZ2E57aovqP5jaGyZ30Oc1hsUS6r7zyj08rMRdzhv+1jERHnoc1ci5UeRkFxJfCz8sY1Uwsn9FpUY9Q1lwhhpmJINvayzJENkp6DqaDCLvHPIEcBDB/QF1fEJLxN7i1f5Lp8LfJuSzvFHwgQceZDfcyGiblHoJKudpPpp7OJSWMvuPS1XD93GaUzcJLt1Dxjn2VCDqtSmBFCTZIvawGRn9NxdkCNAWWDIZZEQVVF0agX0MG/yjYm419eQx3JiXvZcbxKdXvLPID3YHDkqpx5w0fl0jUJbpY9ExmEeCXsHhQqTxmot3p3KzFg==";
        $apiv3Key = 'EToRd3MnsaANSZFzbcrOiR3Q2c01PhdS';
        $nonce = 'ppuezvTya4eS';
        $aad = 'transaction';
        $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);
        echo "<pre>";
        print_r($inBodyResource);
        echo "</pre>";
        exit;
    }
    // 证书验签
    public static function rsaVerify(){
        $inWechatpayTimestamp = '1737182902';
        $inWechatpayNonce = 'P9fZ25SDvKuBuzmW6vkvhUVPvcJDeDP9';
        $inBody = '{"id":"a995400b-ea55-5ce8-8b0f-afac2cd941d8","create_time":"2025-01-18T14:47:51+08:00","resource_type":"encrypt-resource","event_type":"TRANSACTION.SUCCESS","summary":"分账","resource":{"original_type":"transaction","algorithm":"AEAD_AES_256_GCM","ciphertext":"inaWsse8SApUMcv+ABkMAw540EhGNNj63kLHTIuJIBKyWEhNWevtymonACSRCOC9fhFHlBggqbBoavkd0O/boFNS7ohgmaZwfBCY3AOAEqGFE3Fqn93hCj+En6FKxZnjluK12DPkmXV66Pj6Y0GIjL5YcvPdDHFAqatrY4GQEnAgBnd/HxlTCjwHkwh5jCD/Zk7QhpM3N9hh9c+4fmo90HexAuMQT2EwPv+EshYVBf6B717ukvE4YEKSjOjI5mXYqIgMbueTLgfX/AN+RE/imtTlPsTh0Z9/RhdoYspIuT8cQyDY7YLig9sseT6pizbdUbdaFPAdiXgHJshRGzc+++BiE/PWagZkGhf1nH9q5TfvbEojinhFCYy9jxINIhedIp+AKM+csXZ1F4vdV7QCdyyOUGA2pagqqKnER2VM+WLKBebTsXThj/ur7YJ16Q==","associated_data":"transaction","nonce":"sAf1ukp4zVwQ"}}';
        $inWechatpaySignature = 'BNMhxtEadXeV7ph+qqHj1GUlFDzPQ5iJSCFkC3W9Y1EjAFYB4Nm8Ml905UzLbFfxi8v0W68KS0f3fE3yW3WbzcFcWhQcW0Hvc6QAgQeQl69qNgUq9A0bxblHS8o00xFnB6wGluNx2ki5kYTcYXrQG9E2+P/y3ZWcY5qcpzdxtYRdZV0knJxfavErSLI9BQcKm4/5cLMmiR+fgiv02ZHXQU6dGwlizUw/G50W/Z36rgPocNQr+a6Vzvpq5Nc4az4aplsMciaUxysWrVfl2Iv6FH8Kesl8dHJi/aToQ+YOIZKadwcksX3OqWLie5/jQUU3n9EVnXxMkILvvFJQDGvJ+g==';

        $platformCertsFileFilePath = '/www/wwwroot/hotel.rongbaokeji.com/config/cert.pem';
        if(!file_exists($platformCertsFileFilePath)){
            die('找不到证书文件');
        }
        $cert_info = file_get_contents($platformCertsFileFilePath);
        $platformPublicKeyInstance = Rsa::from('file://'.$platformCertsFileFilePath, Rsa::KEY_TYPE_PUBLIC);
        $verifiedStatus = Rsa::verify(
            Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
            $inWechatpaySignature,
            $platformPublicKeyInstance
        );
        echo "<pre>";
        var_dump($verifiedStatus);
        echo "</pre>";
        exit;
    }
    /**
     * 获取证书
     * @return mixed
     */
    public static function certificates(){
        self::jiemi();
        //请求参数(报文主体)
        $headers = self::sign('GET','https://api.mch.weixin.qq.com/v3/certificates','');
        $result = self::curl_get('https://api.mch.weixin.qq.com/v3/certificates',$headers);
        $result = json_decode($result,true);
        $cert_arr = [];
        foreach ($result['data'] as $key => $cert){
            $encrypt_certificate = $cert['encrypt_certificate'];
            $cert_arr[$cert['serial_no']] = self::decryptToString($encrypt_certificate['associated_data'],$encrypt_certificate['nonce'],$encrypt_certificate['ciphertext']);
        }
        $aa = self::decryptToString($result['data'][1]['encrypt_certificate']['associated_data'],$result['data'][0]['encrypt_certificate']['nonce'],$result['data'][0]['encrypt_certificate']['ciphertext']);
        //return response()->json($result);
        echo "<pre>";
        print_r($cert_arr);
        echo "</pre>";
        exit;
        //解密后的内容，就是证书内容
        dd('已被关闭');
    }
    /**
     * 签名
     * @param string $http_method    请求方式GET|POST
     * @param string $url            url
     * @param string $body           报文主体
     * @return array
     */
    public static function sign($http_method = 'POST',$url = '',$body = ''){
        $mch_private_key = self::getMchKey();//私钥
        $timestamp = time();//时间戳
        $nonce = self::getRandomStr(32);//随机串
        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        //构造签名串
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";//报文主体
        //计算签名值
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        //设置HTTP头
        $config = self::config();
        $token = sprintf('WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $config['mchid'], $nonce, $timestamp, $config['serial_no'], $sign);
        $headers = [
            'Accept: application/json',
            'User-Agent: */*',
            'Content-Type: application/json; charset=utf-8',
            'Authorization: '.$token,
        ];
        return $headers;
    }
    //私钥
    public static function getMchKey(){
        $config = config('wechat.min2');
        //path->私钥文件存放路径
        return openssl_get_privatekey(file_get_contents($config['key_path']));
    }
    /**
     * 获得随机字符串
     * @param $len      integer       需要的长度
     * @param $special  bool      是否需要特殊符号
     * @return string       返回随机字符串
     */
    public static function getRandomStr($len, $special=false){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );

        if($special){
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }

        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for($i=0; $i<$len; $i++){
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }
    /**
     * 配置
     */
    public static function config(){
        $config = config('wechat.min2');
        return [
            'appid' => $config['app_id'],
            'mchid' => $config['mch_id'],//商户号
            'serial_no' => $config['serial_no'],//证书序列号
            'description' => '融宝科技',//应用名称（随意）
            'notify' => $config['notify_url'],//支付回调
        ];
    }
//get请求
    public static function curl_get($url,$headers=array())
    {
        $info = curl_init();
        curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_NOBODY,0);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($info,CURLOPT_SSL_VERIFYHOST,false);
        //设置header头
        curl_setopt($info, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($info,CURLOPT_URL,$url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

    const KEY_LENGTH_BYTE = 32;
    const AUTH_TAG_LENGTH_BYTE = 16;
    /**
     * Decrypt AEAD_AES_256_GCM ciphertext
     *
     * @param string    $associatedData     AES GCM additional authentication data
     * @param string    $nonceStr           AES GCM nonce
     * @param string    $ciphertext         AES GCM cipher text
     *
     * @return string|bool      Decrypted string on success or FALSE on failure
     */
    public static function decryptToString($associatedData, $nonceStr, $ciphertext) {
        $config = config('wechat.min2');
        $aesKey = $config['key'];
        $ciphertext = \base64_decode($ciphertext);
        if (strlen($ciphertext) <= self::AUTH_TAG_LENGTH_BYTE) {
            return false;
        }

        // ext-sodium (default installed on >= PHP 7.2)
        if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available()) {
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()) {
            return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // openssl (PHP >= 7.1 support AEAD)
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext = substr($ciphertext, 0, -self::AUTH_TAG_LENGTH_BYTE);
            $authTag = substr($ciphertext, -self::AUTH_TAG_LENGTH_BYTE);

            return \openssl_decrypt($ctext, 'aes-256-gcm', $aesKey, \OPENSSL_RAW_DATA, $nonceStr,
                $authTag, $associatedData);
        }

        throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }
}
