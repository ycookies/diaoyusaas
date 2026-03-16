<?php

namespace App\Merchant\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Hotel\WxappConfig;
/**
 * 小程序生成二维码
 */
class MoveController extends AdminController
{

    // 律师发短信的h5页面
    public function p1(Request $request){
        $config = WxappConfig::getConfig(143);
        $WxConfigArr = [];
        /*$config = [
            'app_id' => 'wxef576cd8ad4cc3c0',
            'secret' => '3bd5d0bf6bb501aa963fd0c82aeaabb6',
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat.log',
            ],
        ];*/

        $app = Factory::miniProgram($config);
        //$jsoninfo = $app->jssdk->buildConfig(['miniProgram.navigateTo'], $debug = false, $beta = false, $json = true,$openTagList = []);
        //$WxConfigArr = json_decode($jsoninfo,true);
        $accessToken = $app->access_token;
        $token = $accessToken->getToken();

        $access_token = $token['access_token'];
        $params = [
            //'access_token' => $access_token,
            'path' => '/pages/index/index',
            'query' => 'phone=17681849188'
        ];
        $res = $this->WxHttpsCurl('https://api.weixin.qq.com/wxa/generate_urllink?access_token='.$access_token,$params,true);
        $resarr = json_decode($res,true);
        $url_link = '';
        if(isset($resarr['errcode']) ||  $resarr['errcode'] == 0){
            $url_link = $resarr['url_link'];
        }
        return view('move.smstolawyer',compact('url_link'));
    }

    public  function WxHttpsCurl($url, $data = null,$ispost = true) {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if($ispost){
                if (!empty($data) && is_array($data)) {
                    $data = json_encode($data, JSON_UNESCAPED_SLASHES);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
            }
            if (!isset($_SERVER["SERVER_NAME"]) && empty($_SERVER["SERVER_NAME"])) {
                $appurl = env('APP_URL');
            } else {
                $appurl = $_SERVER["SERVER_NAME"];
            }
            curl_setopt($curl, CURLOPT_REFERER, 'http://' . $appurl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        } catch (\Exception $exception) {
            info('Curl错误:' . $exception->getMessage() . ' . 行号:' . $exception->getLine());
        }
    }
}
