<?php

namespace App\Services;

/**
 * 喜大收银台服务
 * Class CunZhengService
 * @package App\Services
 * anthor Fox
 */
class SignerService {
    /**
     * appid
     * @var string
     */
    private $appid = '8921474';

    /**
     * 秘钥
     * @var string
     */
    private $secret = '8ddcff3a80f4189ca1c9d4d902c3c909';

    public $apihost = 'https://pay.tuijieip.com/api/pay/h5';
    public $apihost2 = 'https://pay.tuijieip.com/api/pay/wxMiniProgram';

    /**
     * 解签
     * @param array $params
     * @return bool
     */
    public function verify(array $params): bool {
        if (empty($params)) {
            return false;
        }
        $sign = $params['sign'] ?? '';
        if (empty($sign)) {
            return false;
        }
        unset($params['sign']);
        if ($sign == $this->create($params)) {
            return true;
        }
        return false;
    }

    /**
     * 加签
     * @param array $params
     * @return string
     */
    public function create(array $params): string {
        $params['app_key'] = $this->appid;
        foreach ($params as $key => $val) {
            if (is_object($val) || is_array($val) || strlen($val) > 10000 || strtolower($val) == 'array') {
                unset($params[$key]);
            }
        }
        ksort($params);
        $plain_text = '';
        foreach ($params as $key => $val) {
            $plain_text .= $key . $val;
        }

        if ($this->secret) {
            $plain_text = $this->secret . $plain_text . $this->secret;
        }
        info('加密串');
        info($plain_text);
        $plain_text = strtolower($plain_text);

        info($plain_text);

        return md5($plain_text);
    }

    /**
     * @desc 返回喜大收银台中间页
     * @param $params
     * author eRic
     * dateTime 2022-08-12 17:21
     */
    public function prePay($params){
        $params['sign'] = $this->create($params);
        $url =  $this->apihost.'?'.http_build_query($params);
        //$res = HttpsCurl($url,[]);
        return $url;
    }

    /**
     * @desc 返回喜大收银台中间页
     * @param $params
     * author eRic
     * dateTime 2022-08-12 17:21
     */
    public function prePayPam($params){
        $params['sign'] = $this->create($params);
        $url =  $this->apihost2.'?'.http_build_query($params);
        //$res = HttpsCurl($url,[]);
        return $url;
    }

    /**
     * @param $params
     * @return bool
     */
    public static function response($params): bool {
        $singer = new self();
        $result = $singer->verify($params);
        if (!$result) {
            //abort(ApiResponse::error('签名错误'));
            die('签名错误');
        }
        return true;
    }
}