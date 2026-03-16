<?php

namespace App\Admin\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Models\Hotel\WxCardCodePre;
use Illuminate\Support\Facades\Cache;


class WxopenViewPermission extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        return JsonResponse::make()->data([])->error('删除失败:');
    }



    public function default() {

        $data = [];
        return $data;
    }

    public function form() {

        $wxOpen = app('wechat.open');
        $account_info = $wxOpen->getAuthorizer('',$this->payload['hotel_id'],$this->payload['app_type']);
        echo "<pre>";
        print_r($account_info);
        echo "</pre>";
        exit;
        $this->hidden('hotel_id')->value($this->payload['hotel_id']);
        $this->html('<h3>会员卡号编排规则说明:</h3><br> 当前年份日期+时间戳+酒店ID+1位随机数 <br> 共20位纯数字 <br><br> 每次提交随机1000个卡号 <br>');

        $this->disableResetButton();
        //$this->disableSubmitButton();
    }
}
