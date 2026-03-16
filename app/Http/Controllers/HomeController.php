<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Models\Hotel\WxappConfig;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function goldplan(Request $request){
        //$config = WxappConfig::getConfig(143);
        info('点击计划');
        info($request->all());
        $out_trade_no = $request->get('out_trade_no','');
        $sub_mch_id = $request->get('sub_mch_id','');
        //$app = Factory::miniProgram($config);
        $hotel_id = '';
        $app = app('wechat.open')->submchidMiniProgram($sub_mch_id);

        $oauthinfo = app('wechat.open')->getOauthInfo('','',$sub_mch_id);
        //$jsoninfo = $app->jssdk->buildConfig(['miniProgram.navigateTo'], $debug = false, $beta = false, $json = true,$openTagList = []);
        //$WxConfigArr = json_decode($jsoninfo,true);
        $accessToken = $app->access_token;
        $token = $accessToken->getToken();

        if(!empty($token['access_token'])){
            $access_token = $token['access_token'];
        }
        if(!empty($token['authorizer_access_token'])){
            $access_token = $token['authorizer_access_token'];
        }

        $params = [
            //'access_token' => $access_token,
            'path' => '/pages2/extend/pay_order_detail',
            'query' => 'phone=17681849188&out_trade_no='.$out_trade_no
        ];
        $url_link = '';
        // url 打开小程序
        $res = $this->WxHttpsCurl('https://api.weixin.qq.com/wxa/generate_urllink?access_token='.$access_token,$params,true);
        $resarr = json_decode($res,true);
        $url_link = '';
        if(isset($resarr['errcode']) ||  $resarr['errcode'] == 0){
            $url_link = $resarr['url_link'];
        }

        /*
         * 目前只开放给电商类目小程序，具体包含以下一级类目：电商平台、商家自营、跨境电商。
         * $params2 = [
            //'access_token' => $access_token,
            'page_url' => '/pages/index/index?phone=17681849188&out_trade_no='.$out_trade_no,
            'page_title' => '融宝易住大酒店订房',
            'is_permanent' => false,
        ];
        $res = $this->WxHttpsCurl('https://api.weixin.qq.com/wxa/genwxashortlink?access_token='.$access_token,$params2,true);
        $resarr = json_decode($res,true);
        $url_link = '';
        if(isset($resarr['errcode']) ||  $resarr['errcode'] == 0){
            $url_link = $resarr['link'];
        }*/

        return view('goldplan',compact('url_link','out_trade_no','sub_mch_id','oauthinfo'));
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
