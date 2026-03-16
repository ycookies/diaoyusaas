<?php


namespace App\Services;

require_once app_path('libary/Nuonuo/lib/Api.php');

use App\libary\Nuonuo\Api;
use Illuminate\Support\Str;
use App\Models\Hotel\Setting;
use App\Models\Hotel\InvoiceToken;
/**
 * 诺诺发票
 * @package App\Services
 * anthor Fox
 */
class NuonuoService extends BaseService {
    public $config;
    public $callbackurl; // 异步通知地址
    public $redirectUri;
    /*public $config = [
        'appKey'    => "78607195",
        'appSecret' => "26A860C451744A21",
        'token'     => "77f21f617f87135fcd97ef1ueud3dxdi",// please store the token and refresh it before expired
        'taxnum'    => "339901999999199",
        'url'       => "https://sdk.nuonuo.com/open/v1/services",
        'redirectUri' => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
    ];

    // 正式应用参数
    // 授权成功后的样例：https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify?code=01eba82a12d603f7d6940636184b93f6&taxnum=91440300582733406C
    public $config2 = [
        'appKey'    => "36571968",
        'appSecret' => "38B5939841F24733",
        'token'     => "5c454147685b6e7f5b3bcc17miivhwoi",// please store the token and refresh it before expired
        'taxnum'    => "91440300582733406C",
        'url'       => "https://sdk.nuonuo.com/open/v1/services",
        'redirectUri' => 'https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify',
    ];*/

    public function __construct(string $user_id = '') {
        $formdata = Setting::getlists([], 'invoice_configs');
        $field = $formdata['default_debug'];
        $config = [
            'appKey'    => $formdata[$field.'_appKey'],
            'appSecret' => $formdata[$field.'_appSecret'],
            'url'       => $formdata[$field.'_apiUrl'],
            'redirectUri' => $formdata[$field.'_callbackurl'],
        ];
        $this->redirectUri = $formdata[$field.'_callbackurl'];
        $this->callbackurl = $formdata[$field.'_callbackurl'];
        $this->config = $config;
        parent::__construct($user_id);
    }

    // 配置酒店ID
    public function setHotel($hotel_id){
        $info = InvoiceToken::where(['hotel_id'=> $hotel_id])->first();
        if(!$info){
            return false;
        }
        if($info->status != 1){ // 已经关闭开票
            return false;
        }
        $this->config['taxnum'] = $info->salerTaxNum;
        $this->config['token'] = $info->access_token;
        return $this;
    }

    /**
     *全电测试：
     * 税号： 339901999999199
     * APPkey： 78607195
     * APPSecret： 26A860C451744A21
     * Token： 77f21f617f87135fcd97ef1ueud3dxdi
     * 分机号： 923 可以开数电票
     * 分机号： 888 可开普票、专票等发票
     * ISV 发票校验接口调用示例
     */
    public function CheckEInvoice() {
        $method    = "nuonuo.electronInvoice.CheckEInvoice"; // change to your method
        $body      = ['invoiceSerialNum' => '12345678'];
        //$res = Api::sendPostSyncRequest($url, $senid, $appKey, $appSecret, $token, $taxnum, $method, $body);
        $res = $this->sendApiPost($method,$body);
        return $res;
    }


    public function sendApiPost($method,$data){
        $appKey    = $this->config['appKey'];
        $appSecret = $this->config['appSecret'];
        $token     = $this->config['token'];// please store the token and refresh it before expired
        $taxnum    = $this->config['taxnum'];
        $url       = $this->config['url']; // change to online domain
        $senid     = Str::random(32);
        $body = is_array($data) ? json_encode($data): $data;
        $res = Api::sendPostSyncRequest($url, $senid, $appKey, $appSecret, $token, $taxnum, $method, $body);
        addlogs($method,$data,$res);
        return $res;
    }

    // 获取商户授权链接
    public function getOauthUrl($hotel_id){
        $OauthUrl = 'https://open.nuonuo.com/authorize?appKey=36571968&response_type=code&state='.$hotel_id.'&redirect_uri='.urlencode('https://hotel.rongbaokeji.com/hotel/notify/invoiceNotify');
        return $OauthUrl;
    }

    public function getMerchantToken() {
        $appKey    = "36571968";
        $appSecret = "38B5939841F24733";
        $res       = Api::getMerchantToken($appKey, $appSecret);
        return $res;
    }

    public function getISVToken($code) {
        $appKey      = $this->config2['appKey'];
        $appSecret   = $this->config2['appSecret'];
        $taxnum      = $this->config2['taxnum'];
        $redirectUri = $this->config2['redirectUri'];
        $res         = Api::getISVToken($appKey, $appSecret, $code, $taxnum, $redirectUri);
        return $res;
    }

    public function refreshISVToken() {
        $refreshToken = "your.token";
        $appSecret    = "your.appSecret";
        $userId       = "your.userId";
        $res          = Api::refreshISVToken($refreshToken, $userId, $appSecret);
        return $res;
    }


}