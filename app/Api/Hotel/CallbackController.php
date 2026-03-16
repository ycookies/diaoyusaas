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
use App\Models\Hotel\Kefucenter;
// 微信小程序
class CallbackController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;
    public $openPlatform_obj;
    public $message_content;
    public $app_id;

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

    // 微信开放平台 消息通知
    public function callbackOpen(Request $request, $id) {
        info('开放平台 通知信息');
        info('App_id:' . $id);
        $this->app_id = $id;
        //info($request->all());

        $openPlatform           = wxopen();
        $this->openPlatform_obj = $openPlatform;

        $miniProgram = $openPlatform->miniProgram($id, 'Refresh-token');
        $server      = $miniProgram->server; //这里的 server 为授权方的 server，而不是开放平台的 server，请注意！！！

        $server->push(function ($message) {
            info('开放平台 消息内容');
            info($message);

            // 初次验证
            if (!empty($message['Content']) && strpos($message['Content'], 'QUERY_AUTH_CODE') !== false) {
                $query_auth_code = str_replace('QUERY_AUTH_CODE:', '', $message['Content']);
                $authorizer      = $this->openPlatform_obj->handleAuthorize($query_auth_code);
                $authorizerInfo  = $authorizer['authorization_info'];
                //一通操作下来，就得到了微信测试账号的 official_refresh_token

                $wxTest_appid         = $authorizerInfo['authorizer_appid'];
                $wxTest_refresh_token = $authorizerInfo['authorizer_refresh_token'];

                //server方：微信测试公共号
                $wxTest_account = $this->openPlatform_obj->miniProgram($wxTest_appid, $wxTest_refresh_token);

                $outMessage = $query_auth_code . '_from_api';

                $outMessage_text = new Text($outMessage);
                //用wx的server回复吧。注意这里用客服消息回复
                $customer_service = $wxTest_account->customer_service;
                $mk               = $customer_service->message($outMessage_text)->from($message['ToUserName'])
                    ->to($message['FromUserName'])
                    ->send();
                return $mk;
            }

            if (!empty($message['MsgType'])) {
                // 事件处理
                if (!empty($message['Event'])) {

                    $method = camelCase('handle_' . $message['MsgType']);
                    if (method_exists($this, $method)) {
                        $this->openid = $message['FromUserName'];
                        return call_user_func_array([$this, $method], [$message]);
                    }
                    /*switch ($message['Event']) {
                        case 'weapp_audit_fail': // 小程序代码 审核未通过
                            return $this->weapp_audit_fail($message);
                            break;
                        case 'weapp_audit_success': // 小程序代码 审核通过
                            return $this->weapp_audit_success($message);
                            break;
                        default:
                            break;
                    }*/
                } else {
                    try {
                        $oauthinfo = WxopenMiniProgramOauth::where(['AuthorizerAppid'=> $this->app_id])->first();
                    } catch (\Error $error) {

                    } catch (\Exception $exception) {

                    }
                    $hotel_id = !empty($oauthinfo->hotel_id) ? $oauthinfo->hotel_id:0;
                    $platform = !empty($oauthinfo->app_type) ? $oauthinfo->app_type:0;
                    $msg_content = '';
                    switch ($message['MsgType']) {
                        case 'text':
                            $msg_content = $message['Content'];
                            //return '你的消息已经收到,人工客服繁忙,请稍候!';
                            break;
                        case 'image':
                            $fileurl = Kefucenter::downMedia($hotel_id,$platform,$message['MediaId']);
                            if(!empty($fileurl)){
                                $msg_content = $fileurl;
                            }else{
                                $msg_content = $message['MediaId'];
                            }

                            //return '收到图片消息';
                            break;
                        case 'voice':
                            $fileurl = Kefucenter::downMedia($hotel_id,$platform,$message['MediaId']);
                            if(!empty($fileurl)){
                                $msg_content = $fileurl;
                            }else{
                                $msg_content = $message['MediaId'];
                            }
                            //return '收到语音消息';
                            break;
                        case 'video':
                            $fileurl = Kefucenter::downMedia($hotel_id,$platform,$message['MediaId']);
                            if(!empty($fileurl)){
                                $msg_content = $fileurl;
                            }else{
                                $msg_content = $message['MediaId'];
                            }
                            //return '收到视频消息';
                            break;
                        case 'location':
                            //return '收到坐标消息';
                            break;

                        default:
                            break;
                    }


                    $msgdata = [
                        'hotel_id' => $hotel_id,
                        'app_id' => $this->app_id,
                        'platform' => $platform,
                        'user_openid' => $message['FromUserName'],
                        'msg_to' => 1,
                        'msg_type' => $message['MsgType'],
                        'msg_content' => $msg_content,
                    ];
                    Kefucenter::addMsg($msgdata);
                }

            }
            // return 'Welcome!';
        });
        return $server->serve();
    }

    /**
     * 事件引导处理方法（事件有许多，拆分处理）
     *
     * @param $event
     *
     * @return mixed
     */
    protected function handleEvent($event)
    {
        $method = camelCase('event_' . $event['Event']);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$event]);
        }
        return '';
    }

    // 小程序代码 审核未通过
    public function eventWeappAuditFail($message) {
        $where = [
            ['ToUserName' ,'=', $message['ToUserName']],
            ['auditid','<>','']
        ];
        $info               = WxopenMiniProgramVersion::where($where)->orderBy('id', 'DESC')->first();
        $info->audit_status = 3;
        $info->fail_reason  = !empty($message['Reason']) ? $message['Reason'] : '-';
        $info->save();
        return true;
    }

    // 小程序代码 审核通过
    public function eventWeappAuditSuccess($message) {
        $where = [
            ['ToUserName' ,'=', $message['ToUserName']],
            ['auditid','<>','']
        ];
        $info               = WxopenMiniProgramVersion::where($where)->orderBy('id', 'DESC')->first();
        $info->audit_status = 4;
        $info->save();
        return true;
    }

    /**
     * 订阅
     *
     * @param $event
     *
     * @throws \Throwable
     */
    protected function eventSubscribe($event)
    {
        info('扫描带参二维码事件');
        info($event);
        // 'ToUserName' => 'gh_862a210a4c87',
        //  'FromUserName' => 'o5wESxLOulsmwCSg5l_1J6KpPnro',
        //  'CreateTime' => '1711552664',
        //  'MsgType' => 'event',
        //  'Event' => 'SCAN',
        //  'EventKey' => 'merchant_200018',
        //  'Ticket' => 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd29pcmNPRDFjaTQxOWk5NU5DYzgAAgTSNwRmAwSAUQEA',
        $EventKey = explode('_',$event['EventKey']);
        switch ($EventKey[0]) {
            // 商家账号绑定微信
            case 'merchant':
                /*$user = Muser::find($EventKey[1]);
                if(!empty($user->wx_openid)){
                    return '本账号已经绑定了其它微信,如需更换请联系平台客服!';
                }
                Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], 'ok');*/
                return '账号绑定微信成功,感谢你使用 '.env('APP_NAME');
                break;
            // 商家账号绑定微信
            case 'merchantLogin':
                //Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], $event['FromUserName']);
                return '账号已登陆';
                break;
            default:
                break;
        }
        return '感谢你关注';

    }

    /**
     * 取消订阅
     *
     * @param $event
     */
    protected function eventUnsubscribe($event)
    {

    }


    /**
     * 扫描带参二维码事件
     *
     * @param $event
     */
    public function eventSCAN($event)
    {
        info('扫描带参二维码事件');
        info($event);
        // 'ToUserName' => 'gh_862a210a4c87',
        //  'FromUserName' => 'o5wESxLOulsmwCSg5l_1J6KpPnro',
        //  'CreateTime' => '1711552664',
        //  'MsgType' => 'event',
        //  'Event' => 'SCAN',
        //  'EventKey' => 'merchant_200018',
        //  'Ticket' => 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd29pcmNPRDFjaTQxOWk5NU5DYzgAAgTSNwRmAwSAUQEA',
        $EventKey = explode('_',$event['EventKey']);
        switch ($EventKey[0]) {
            // 商家账号绑定微信
            case 'merchant':
                /*$user = Muser::find($EventKey[1]);
                if(!empty($user->wx_openid)){
                    return '本账号已经绑定了其它微信,如需更换请联系平台客服!';
                }
                Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], 'ok');*/
                return '账号绑定微信成功,感谢你使用 '.env('APP_NAME');
                break;
            // 商家账号绑定微信
            case 'merchantLogin':
                //Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], $event['FromUserName']);
                return '账号已登陆';
                break;
            default:
                break;
        }
        return '感谢你关注';
    }

    // 卡券审核事件 事件类型，card_pass_check(卡券通过审核)
    public function eventCardPassCheck($event){
        info('卡券通过审核1');
        info($event);
    }
    // 卡券审核事件 事件类型  card_not_pass_check（卡券未通过审核）
    public function eventCardNotPassCheck($event){
        info('卡券未通过审核1');
        info($event);
    }

    // 卡券用户领取事件 事件类型  user_get_card（用户领取卡券）
    public function eventUserGetCard($event){
        info('用户领取卡券1');
        info($event);
    }

    // 会员卡激活事件推送  submit_membercard_user_info（会员卡激活事件推送）
    public function eventSubmitMembercardUserInfo($event){
        info('会员卡激活事件推送1');
        info($event);
    }

    // 卡券核销事件 user_consume_card(核销事件)）
    public function eventUserConsumeCard($event){
        info('核销事件1');
        info($event);
    }
    // 进入会员卡事件推送 user_view_card(用户点击会员卡)
    public function eventUserViewCard($event){
        info('用户点击会员卡1');
        info($event);
    }

    // 会员卡 库存报警事件 card_sku_remind库存报警
    public function eventCardSkuRemind($event){
        info('会员卡 库存报警1');
        info($event);
    }

}
