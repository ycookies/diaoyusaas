<?php

namespace App\Api\Hotel;

use App\Admin;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use App\Models\MerchantUser as Muser;
use function React\Promise\all;

// 商户 微信公众号 消息通知
class GzhSellerNotifyController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    // 酒店公众号 异步通知
    public function notify(Request $request,$id) {
        info('酒店公众号 异步通知');
        info($request->all());
        $wxOpen     = app('wechat.open');
        $app     = $wxOpen->wxgzh($id);
        //$app = Factory::officialAccount(config('wechat.gzh2.default'));
        // 验证token
        $response = $app->server->serve();
        return $response;

        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    $method = camelCase('handle_' . $message['MsgType']);

                    if (method_exists($this, $method)) {
                        $this->openid = $message['FromUserName'];
                        return call_user_func_array([$this, $method], [$message]);
                    }
                    return '收到事件消息';
                    break;
                case 'text':
                    info('文字消息');
                    info($message);
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

        });
        $response = $app->server->serve();

        return $response;
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
                $user = Muser::find($EventKey[1]);
                if(!empty($user->wx_openid)){
                    return '本账号已经绑定了其它微信,如需更换请联系平台客服!';
                }
                Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], 'ok');
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
                $user = Muser::find($EventKey[1]);
                if(!empty($user->wx_openid)){
                    return '本账号已经绑定了其它微信,如需更换请联系平台客服!';
                }
                Muser::where(['id'=> $EventKey[1]])->update(['wx_openid'=>$event['FromUserName'],'is_wxgzh_subscribe'=>1]);
                \Cache::put($event['EventKey'], 'ok');
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
        return '感谢你关注 ';
    }

    // 卡券审核事件 事件类型，card_pass_check(卡券通过审核)
    public function eventCardPassCheck($event){
        info('卡券通过审核');
        info($event);
    }
    // 卡券审核事件 事件类型  card_not_pass_check（卡券未通过审核）
    public function eventCardNotPassCheck($event){
        info('卡券未通过审核');
        info($event);
    }

    // 卡券用户领取事件 事件类型  user_get_card（用户领取卡券）
    public function eventUserGetCard($event){
        info('用户领取卡券');
        info($event);
    }

    // 会员卡激活事件推送  submit_membercard_user_info（会员卡激活事件推送）
    public function eventSubmitMembercardUserInfo($event){
        info('会员卡激活事件推送');
        info($event);
    }

    // 卡券核销事件 user_consume_card(核销事件)）
    public function eventUserConsumeCard($event){
        info('核销事件');
        info($event);
    }
    // 进入会员卡事件推送 user_view_card(用户点击会员卡)
    public function eventUserViewCard($event){
        info('用户点击会员卡');
        info($event);
    }

    // 会员卡 库存报警事件 card_sku_remind库存报警
    public function eventCardSkuRemind($event){
        info('会员卡 库存报警');
        info($event);
    }

    // 从卡券进入公众号会话事件推送 user_enter_session_from_card
    public function eventUserEnterSessionFromCard($event){
        info('从卡券进入公众号会话事件推送');
        info($event);
    }
}
