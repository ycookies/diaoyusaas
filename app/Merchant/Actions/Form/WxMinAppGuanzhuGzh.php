<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Widgets\Alert;

class WxMinAppGuanzhuGzh extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        $validator = \Validator::make($input, [
            'wxa_subscribe_biz_flag'  => 'required',
            //'type'=> 'required',
        ], [
            'wxa_subscribe_biz_flag.required'  => '请选择 不能为空',
            //'type.required'  => '操作类型 不能为空',
        ]);
        if ($validator->fails()) {
            return JsonResponse::make()->error($validator->errors()->first());
        }
        $wxa_subscribe_biz_flag = $input['wxa_subscribe_biz_flag'];

        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $oauthinfo = $openPlatform->getOauthInfo('',Admin::user()->hotel_id,'','wxgzh');
        if(empty($oauthinfo->AuthorizerAppid)){
            return JsonResponse::make()->error('你还未操作公众号授权');
        }
        $app_id = $oauthinfo->AuthorizerAppid;
        if(empty($wxa_subscribe_biz_flag)){
            $app_id = false;
        }
        $res = $miniProgram->setting->setDisplayedOfficialAccount($app_id);

        addlogs('wxaUpdateshowwxaitem',['wxa_subscribe_biz_flag'=> $wxa_subscribe_biz_flag,'app_id'=> $app_id],$res);
        if(isset($res['errcode']) && $res['errcode'] == 0){
            return JsonResponse::make()->data($res)->success('已提交成功')->refresh();
        }
        $emsg = '提交失败，请稍候再试';
        return JsonResponse::make()->error($emsg);
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {

        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->setting->getDisplayedOfficialAccount();

        $alert = Alert::make("必须是跟小程序同一主体的微信公众号(服务号)<br/>此操作将影响 微信小程序后台 这里的开关。<br/><img src='/img-jiaocheng/minapp-guanzhu-gzh.png' width='320'>", '提示：');
        $this->html($alert->info());
        $this->width(9,3);
        $is_open = 0;
        if(isset($res['errcode']) && $res['errcode'] == 0 && !empty($res['appid'])){
            $is_open = 1;
            $this->html($res['nickname'])->label('已设置公众号')->help();
            $this->html($res['appid'])->label('公众号app_id');
            $this->html("<img src='".$res['headimg']."' width='66'>")->label('公众号logo');
        }else{
            $this->html("<span class='text-danger'>未设置</span>")->label('已设置公众号')
                ->help('如未设置相关联的公众号:<a href="https://mp.weixin.qq.com/wxamp/basicprofile/followMp?token=452821502&lang=zh_CN" target="_blank">>>> 前去小程序后台设置/a>');
        }
        $this->radio('wxa_subscribe_biz_flag', '开启关注公众号组件')
            ->help('是否打开扫码关注组件')
            ->options(['0'=> '关闭','1'=>'开启'])
            /*->when('1',function (Form $form) {
                $form->text('appid', '公众号 appid')->value('')->help('同一主体的公众号');
            })*/
            ->value($is_open)
            ->required();

        $this->disableResetButton();
    }
}
