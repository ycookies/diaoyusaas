<?php

namespace App\Services;

use \App\Models\Hotel\Kefucenter;

/**
 * 客服 服务
 * @package App\Services
 * anthor Fox
 */
class KefuService extends BaseService {

    // 发送消息
    public function sendMsg($user_openid,$send_content){

        $msginfo = Kefucenter::where(['user_openid'=> $user_openid])->orderBy('id','DESC')->first();
        if($msginfo){
            $msg_data = $msginfo->toArray();
            unset($msg_data['id']);
            unset($msg_data['created_at']);
            if(!empty($msg_data['platform_str'])){
                unset($msg_data['platform_str']);
            }
            unset($msg_data['updated_at']);
            $msg_data['msg_type'] = 'text';
            $msg_data['msg_to'] = 2;
            $msg_data['msg_content'] = $send_content;
            Kefucenter::addMsg($msg_data);
            $res = Kefucenter::sendMsg($msginfo->hotel_id,$msginfo->app_id,$msginfo->user_openid,$send_content,$msginfo->platform);
            if(isset($res['errcode']) && $res['errcode'] != 0){
                return false;
            }
        }
        return true;
    }
}