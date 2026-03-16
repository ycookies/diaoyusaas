<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class WxMinAppFabuForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram  = $openPlatform->miniProgram($oauth->AuthorizerAppid, $oauth->authorizer_refresh_token);

        $res1          = $miniProgram->code->release();
        addlogs('miniProgram_code_release',['app_id'=> $oauth->AuthorizerAppid],$res1);
        if (isset($res1['errcode']) && $res1['errcode'] != 0) {
            return JsonResponse::make()->data($res1)->error('全网发布小程序遇到问题:'.$res1['errcode']);
        }
        $model               = WxopenMiniProgramVersion::where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC')->first();
        //$model->auditid      = $res1['auditid'];
        $model->audit_status = 5;
        $model->save();

        return JsonResponse::make()->data($res1)->success('成功！')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {

        //$this->confirm('确认已经填写完整了吗？');
        //$this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        $this->html('<h3>现在全网发布</h3>');

        //$this->display('hotel_name','酒店名')->value($this->payload['name']);
        $this->disableResetButton();
    }
}
