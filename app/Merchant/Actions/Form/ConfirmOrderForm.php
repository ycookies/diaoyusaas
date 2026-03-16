<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use function Swoole\Coroutine\prepare;

class ConfirmOrderForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {


        return JsonResponse::make()->data([])->success('成功！')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {
        $room_type = $this->payload['room_type'];
        $booking_name = $this->payload['booking_name'];
        $booking_phone =$this->payload['booking_phone'];
        //$booking_num = $this->payload['num'];
        $this->width(8,3);
        $this->action('/booking-order-confirm');
        $htmls = '预定客房：'. $room_type .'<br/>预定人：' . $booking_name . '<br/>预定人电话：' . $booking_phone . '<br/>';
        $this->html($htmls)->label('预定信息');
        $this->text('order_no','订单号')->value($this->payload['order_no'])->prepend('')->readOnly();
        $this->radio('confirm_type','确认类型')
            ->options(['1' => '确认接单','2'=> '客房已满 取消订单'])->required();

        //$this->display('hotel_name','酒店名')->value($this->payload['name']);
        $this->disableResetButton();
    }
}
