<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class ViewTiyanQrcodeForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram  = $openPlatform->miniProgram($oauth->AuthorizerAppid, $oauth->authorizer_refresh_token);
        $res          = $miniProgram->code->getCategory();

        $errmsg = !empty($res['errmsg']) ? $res['errmsg']:'';
        if (empty($res['category_list'][0])) {
            return JsonResponse::make()->error('获取小程序类目出错,请前往微信小程序后台查看类目设置情况:'.$errmsg);
        }
        $model               = WxopenMiniProgramVersion::where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC')->first();

        $category_list = $res['category_list'][0];

        $item_list = [];
        // 提交小程序模板审核
        $apidata = [
            'version_desc' => $model->user_desc,
            'item_list' => [
                [
                    //'title' => '', // 小程序页面的标题
                    'tag' => '酒店预订,酒店定房', // 小程序的标签
                    'first_class'  => $category_list['first_class'],
                    'second_class' => $category_list['second_class'],
                    'first_id'     => $category_list['first_id'],
                    'second_id'    => $category_list['second_id'],
                ]
            ],
            'order_path' => '/pages/order/index',
        ];
        $res1          = $miniProgram->code->submitAudit($apidata);
        addlogs('miniProgram_code_submitAudit',$apidata,$res1);
        if (empty($res1['auditid'])) {
            return JsonResponse::make()->data($res1)->error('发布小程序遇到问题:');
        }
        $model->auditid      = $res1['auditid'];
        $model->audit_status = 2;
        $model->save();

        return JsonResponse::make()->data($res1)->success('成功！')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram  = $openPlatform->miniProgram($oauth->AuthorizerAppid, $oauth->authorizer_refresh_token);
        $path         = '/minapp/' . $oauth->AuthorizerAppid . '_tiyan_qrcode.jpg';

        if (file_exists(public_path($path))) {
            unlink(public_path($path));
        }
        $res0 = $miniProgram->tester->bind('Q3664839');
        addlogs('wxa_bind_tester',['tester_id'=>'Q3664839'],$res0);
        $res = $miniProgram->code->getQrCode();
        addlogs('wxminapp_getQrCode',['hotel_id'=>Admin::user()->hotel_id],$res);
        file_put_contents(public_path($path), $res);
        $htmls = '<img src="' . $path . '" width="120" />';

        //$this->confirm('确认已经填写完整了吗？');
        //$this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        $this->width('10','2');
        $this->html('<h3>体验小程序码</h3>');
        $tips = <<<HTML
        <ul>
        <li>使用微信扫码可体验小程序</li>
        <li>如何提示没有权限体验，可添加对应的微信号为：体验人员</li>
        <li class="text-danger">如果点击提交，会将此版本提交到微信去审核，审核通过后可全网发布</li>
</ul>
HTML;

        $this->html($tips);
        $this->html($htmls);
        //$this->display('hotel_name','酒店名')->value($this->payload['name']);
        $this->disableResetButton();
        if (!empty($this->payload['audit_status']) && $this->payload['audit_status'] == 5) {
            $this->disableSubmitButton();
        }
    }
}
