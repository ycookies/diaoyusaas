<?php

namespace App\Http\Controllers\Merchant\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hotel\Kefucenter;
use App\Models\Hotel\Hotel;
// 客服中心
class KefucenterController extends Controller
{

    // 回复消息
    public function index(Request $request)
    {
        $logid = $request->get('logid');
        //$info = Kefucenter::find($logid);
        $info = Kefucenter::where(['user_openid'=> $logid])->orderBy('id','DESC')->first();
        if(!$info){
            return view('merchant.mobile.run.kefucenter-error');
        }
        $hotel_name = Hotel::where(['id'=> $info->hotel_id])->value('name');
        $msglist = Kefucenter::where(['user_openid'=> $logid])->orderBy('id','DESC')->limit(15)->get()->reverse();
        //sort($msglist);
        return view('merchant.mobile.run.kefucenter',compact('msglist','logid','hotel_name'));
    }

    // 发送消息
    public function sendMsg(Request $request){
        $logid = $request->get('logid');
        $send_content = $request->get('send_content');
        $msginfo = Kefucenter::where(['user_openid'=> $logid])->orderBy('id','DESC')->first();
        if($msginfo){
            $msg_data = $msginfo->toArray();
            unset($msg_data['id']);
            unset($msg_data['created_at']);
            unset($msg_data['updated_at']);
            $msg_data['msg_to'] = 2;
            $msg_data['msg_content'] = $send_content;
            Kefucenter::addMsg($msg_data);
            $res = Kefucenter::sendMsg($msginfo->hotel_id,$msginfo->app_id,$msginfo->user_openid,$send_content,$msginfo->platform);
            if(isset($res['errcode']) && $res['errcode'] != 0){
                info('消息发送失败');
                return returnData(204,0,[],'消息发送失败');
            }
        }

        info('发送客服消息',$request->all());
        return returnData(200,1,[],'ok');
    }

    // 客服列表
    public function kefuLists(Request $request){
        //$mk  = Kefucenter::downMedia(225,'minapp','qhmfSgHakiz9dwtP_Dx98mdzTAO2GXAFAdZhE0h0miQzmLw2M0JKoIS-C1stU7rg');

    }
}
