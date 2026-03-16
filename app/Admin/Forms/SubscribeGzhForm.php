<?php

namespace App\Admin\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Admin\Repositories\HotelSettingRep;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Hotel;
// 关注公众号
class SubscribeGzhForm extends Form implements LazyRenderable
{
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input)
    {
        return $this->response()->success('保存成功');
    }

    public function default()
    {

        $data = [];
        return $data;
    }
    public function form()
    {
        //$this->confirm('确认已经填写完整了吗？');
        //$this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        //$form1->html('<h3>分账设置</h3>');

        $weChatFlag  = $this->payload['weChatFlag'];
        if (! $qrcode_url = \Cache::get($weChatFlag)) {
            $app = app('wechat.official_account');
            $result = $app->qrcode->temporary($weChatFlag, 24 * 3600); //一天有效期
            $qrcode_url = $result['url'];
        }
        if(!empty($qrcode_url)){
            \Cache::put($weChatFlag, $qrcode_url, now()->addDay());
            $this->html('<div style="margin-top: 50px;text-align: center">'.\QrCode::size(200)->generate($qrcode_url).'</div>');
        }else{
            $this->html('<h3>获取二维码失败，请检查公众号配置</h3>');
        }
        $this->hidden('weChatFlag','')->value($weChatFlag);
        $this->disableResetButton();
        $this->disableSubmitButton();
    }
}
