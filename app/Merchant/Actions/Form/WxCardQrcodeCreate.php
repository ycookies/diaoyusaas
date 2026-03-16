<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class WxCardQrcodeCreate extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {

        $res1 = [];
        return JsonResponse::make()->data($res1)->success('成功！')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {
        $card_id = $this->payload['card_id']; //$request->get('card_id');
        $seller     = Admin::user();
        $wxOpen     = app('wechat.open');
        $hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj     = $wxOpen->hotelWxgzh($hotel_id);
        $cards = [
            'action_name' => 'QR_CARD',
            'expire_seconds' => 1800,
            'action_info' => [
                'card' => [
                    'card_id' => $card_id,
                    'is_unique_code' => false,
                    'outer_id' => 1,
                ],
            ],
        ];
        $cardInfo = $gzhobj->card->createQrCode($cards);
        if(!empty($cardInfo['show_qrcode_url'])){
            $qrcode_url = $cardInfo['url'];
            $this->html('<h3>供用户扫码后添加卡券到卡包 二维码</h3> <div style="text-align: center">'.\QrCode::size(100)->generate($qrcode_url).'</div>');
            //return JsonResponse::make()->data($cardInfo)->success('获取成功')->refresh();
        }else{
            $error_msg = !empty($cardInfo['errmsg']) ? $cardInfo['errmsg']:'-';
            $this->html('<h3>获取失败：'.$error_msg.'</h3>');
        }

        //return JsonResponse::make()->data($cardInfo)->error('删除失败:'.$error_msg);
        /**
         * "errcode": 0,
        "errmsg": "ok",
        "ticket": "gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA",
        "expire_seconds": 1800,
        "url": "http://weixin.qq.com/q/02y6zwBe6mdoG1f0qFhC4O",
        "show_qrcode_url": "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA"
        }
         */

        //$this->confirm('确认已经填写完整了吗？');
        //$this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        //$this->html('<h3>开发者可调用该接口生成一张卡券二维码供用户扫码后添加卡券到卡包</h3>');

        //$this->display('hotel_name','酒店名')->value($this->payload['name']);
        $this->disableResetButton();
        $this->disableSubmitButton();
    }
}
