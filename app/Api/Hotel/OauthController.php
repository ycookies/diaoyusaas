<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\Setting;
use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use Illuminate\Support\Facades\Cache;

// 授权通知
class OauthController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;
    public $openPlatform_obj;
    public $message_content;

    // 获取开放平台实例
    public function openPlatform() {
        $wxopen_config = Setting::getlists([], 'wxopen');
        $open_config   = [
            'app_id'  => $wxopen_config['wxopen_app_id'],
            'secret'  => $wxopen_config['wxopen_secret'],
            'token'   => !empty($wxopen_config['wxopen_Token']) ? $wxopen_config['wxopen_Token'] : '',
            'aes_key' => !empty($wxopen_config['wxopen_aesKey']) ? $wxopen_config['wxopen_aesKey'] : ''
        ];
        $openPlatform  = Factory::openPlatform($open_config);
        return $openPlatform;
    }

    // 微信开放平台 授权回调
    public function oauthNotify(Request $request) {
        info('授权信息');
        info($request->all());
        if (!empty($request->has('auth_code'))) {
            info('授权信息完成');
            info($request->all());
            $auth_code = $request->get('auth_code');
            $query_auth_code = str_replace('queryauthcode@@@', '', $auth_code);

            $where = [
              'AuthorizationCode' =>$query_auth_code,
            ];
            $info  = WxopenMiniProgramOauth::where($where)->count();
            if(empty($info)){
                $message = $request->all();

                $authorizer      = $this->openPlatform()->handleAuthorize($query_auth_code);
                if (!empty($authorizer['authorization_info'])) {
                    $message = array_merge($authorizer['authorization_info'], $message);
                }

                $message['user_id']  = Request()->get('uid', '1');
                $message['hotel_id'] = Request()->get('hid', '1');
                //info('授权资料数组');
                //info($message);
                $message['AuthorizationCode'] = $query_auth_code;
                $message['AuthorizerAppid'] = $message['authorizer_appid'];
                Cache::put($message['AuthorizationCode'], $message);
                WxopenMiniProgramOauth::addOauth($message);
            }else{
                $updata = [
                    'user_id'  => $request->uid,
                    'hotel_id' => $request->hid,
                    'app_type' => $request->get('app_type')
                ];
                WxopenMiniProgramOauth::where(['AuthorizationCode' => $query_auth_code])
                    ->update($updata);
            }



            $app_type = $request->get('app_type');
            /*if ($app_type == 'wxgzh') {
                return redirect('/merchant/wxgzh');
            }*/
            if ($app_type == 'minapp') {
                return redirect('/merchant/minapp');
            }
        }

        $openPlatform           = $this->openPlatform();
        $this->openPlatform_obj = $openPlatform;
        $server                 = $openPlatform->server;

        // 处理授权成功事件
        $server->push(function ($message) {
            info('授权成功');
            info($message);
            //info(Request()->all());
            if (!empty($message['AuthorizationCode'])) {
                $query_auth_code = str_replace('queryauthcode@@@', '', $message['AuthorizationCode']);
                $authorizer      = $this->openPlatform()->handleAuthorize($query_auth_code);
                if (!empty($authorizer['authorization_info'])) {
                    $message = array_merge($authorizer['authorization_info'], $message);
                }

                $message['user_id']  = Request()->get('uid', '1');
                $message['hotel_id'] = Request()->get('hid', '1');
                info('授权资料数组');
                info($message);
                Cache::put($message['AuthorizationCode'], $message);
                WxopenMiniProgramOauth::addOauth($message);
            }

        }, Guard::EVENT_AUTHORIZED);

        // 处理授权更新事件
        $server->push(function ($message) {
            info('授权更新成功:授权资料');
            info($message);
            if (!empty($message['AuthorizationCode'])) {
                $query_auth_code = str_replace('queryauthcode@@@', '', $message['AuthorizationCode']);
                $authorizer      = $this->openPlatform()->handleAuthorize($query_auth_code);
                if (!empty($authorizer['authorization_info'])) {
                    $message = array_merge($authorizer['authorization_info'], $message);
                }
                $message['user_id']  = Request()->get('uid', '1');
                $message['hotel_id'] = Request()->get('hid', '1');
                info('授权资料数组');
                info($message);
                Cache::put($message['AuthorizationCode'], $message);
                WxopenMiniProgramOauth::upOauth($message);
            }
        }, Guard::EVENT_UPDATE_AUTHORIZED);

        $server->push(function ($message) {
            info('授权取消:授权资料');
            info($message);
            WxopenMiniProgramOauth::unOauth($message);
        }, Guard::EVENT_UNAUTHORIZED);

        return $server->serve();
    }

}
