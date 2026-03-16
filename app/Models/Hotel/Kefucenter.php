<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use EasyWeChat\Kernel\Messages\Text;

class Kefucenter extends HotelBaseModel
{

    const Msg_to = [
       1 => '用户',
       2 => '酒店客服',
       3 => '平台客服',
    ];
    // wxgzh,wxminapp,wxh5,pcweb
    public static $platform = [
      'wxgzh' => '公众号',
      'minapp' => '微信小程序',
      'wxh5' => '微信网页',
      'pcweb' => 'PC网页',
    ];
    public static $msg_type_arr = [
        'text'=> '文本',
        'image'=> '图片',
        'voice'=> '语音',
        'video'=> '视频',
        'shortvideo'=> '小视频',
        'link'=> '链接',
        'file'=> '文件',
    ];
    protected $table = 'hotel_kefucenter';
    protected $guarded = [];
    protected $appends = ['platform_str'];

    public function user() {
        return $this->hasOne(\App\User::class, 'id','user_id')->select('id','name','nick_name','openid','gzh_openid','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    public function getPlatformStrAttribute() {
        return !empty(self::$platform[$this->platform]) ? self::$platform[$this->platform]:'网络平台';
    }

    /**
     * @desc 存储用户咨询消息
     * @param $msg_data
     * author eRic
     * dateTime 2024-09-04 21:14
     */
    public static function addMsg($msg_data){
        $status =  Kefucenter::create($msg_data);
        // 机器人通知
        $platform_str = !empty(self::$platform[$msg_data['platform']]) ? self::$platform[$msg_data['platform']]:'访客';
        // 获取酒店客服机器人地址
        $flds     = ['kefu_center_qywx_robot_url'];
        $formdata = HotelSetting::getlists($flds,$msg_data['hotel_id']);
        if(!empty($formdata['kefu_center_qywx_robot_url'])){
            WxRobotkefuCenter($formdata['kefu_center_qywx_robot_url'],$platform_str.'咨询',$msg_data['msg_content'],$msg_data['platform'],$msg_data['user_openid']);
        }
        return true;
    }

    // 发送消息
    public static function sendMsg($hotel_id,$app_id,$openId,$send_content,$platform){
        if($platform == 'wxgzh'){
            $message = new Text($send_content);
            $officialAccount = app('wechat.open')->hotelWxgzh($hotel_id);
            //$officialAccount = $openPlatform->officialAccount($app_id);
            $result = $officialAccount->customer_service->message($message)->to($openId)->send();
            addlogs('officialAccount_customer_service',[$hotel_id,$app_id,$openId,$send_content],$result);
            return $result;
        }

        if($platform == 'minapp'){
            $message = new Text($send_content);
            $miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
            //$officialAccount = $openPlatform->officialAccount($app_id);
            $result = $miniProgram->customer_service->message($message)->to($openId)->send();
            addlogs('miniProgram_customer_service',[$hotel_id,$app_id,$openId,$send_content],$result);
            return $result;
        }


    }
    // 获取临时素材内容
    public static function downMedia($hotel_id,$platform,$mediaId){
        if($platform == 'wxgzh'){
            $app = app('wechat.open')->hotelWxgzh($hotel_id);
        }

        if($platform == 'minapp'){
            $app = app('wechat.open')->hotelMiniProgram($hotel_id);
        }
        $stream = $app->media->get($mediaId);

        if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            // 以内容 md5 为文件名存到本地
            $dir = '/kefu-media';
            $files = $stream->save(public_path($dir));
            return env('APP_URL').$dir.'/'.$files;
            // 自定义文件名，不需要带后缀
            //$stream->saveAs('保存目录', '文件名');
        }
        return '';
    }
}
