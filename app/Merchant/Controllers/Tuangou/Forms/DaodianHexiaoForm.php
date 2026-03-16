<?php

namespace App\Merchant\Controllers\Tuangou\Forms;


use App\Models\Hotel\Order\Order as mOrder;
use App\Models\Hotel\Order\OrderClerk;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class DaodianHexiaoForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input) {
        if (empty($input['offline_qrcode'])) {
            return JsonResponse::make()->error('请输入核销码');
        }
        $offline_qrcode = $input['offline_qrcode'];
        $orderinfo      = mOrder::where(['offline_qrcode' => $offline_qrcode])->first();
        if (!$orderinfo) {
            return JsonResponse::make()->error('核销码不正确,请检查');
        }
        if (empty($orderinfo->trade_no)) {
            return JsonResponse::make()->error('对应的订单未支付');
        }

        if (!empty($orderinfo->clerk_id)) {
            return JsonResponse::make()->error('核销码，已经核销');
        }
        mOrder::where(['offline_qrcode' => $offline_qrcode])->update(['clerk_id' => Admin::user()->id]);

        // 加入核销记录
        $insdata = [
            'hotel_id'        => $orderinfo->hotel_id,
            'affirm_pay_type' => 1,
            'clerk_type'      => 2,
            'clerk_remark'    => '',
            'user_id'         => $orderinfo->user_id,
            'order_id'        => $orderinfo->id,
        ];
        OrderClerk::create($insdata);
        // 处理后续事项

        // 更新团购订单
        TuangouOrderRelation::upOrderStatus($orderinfo->id, TuangouOrderRelation::Order_status_3);

        // 分账
        if($orderinfo->pay_type == 1){ // 只有在线支付 微信支付 已完成时触发
            $service = new \App\Services\ProfitsharingService();
            $service->profitsharingToBooking($orderinfo->order_no);

        }
        return JsonResponse::make()->data([])->success('核销成功')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {

        $this->text('offline_qrcode', '核销码')
            ->help('客人订单里面可以查看到核销码')
            ->required();

        $this->disableResetButton();

    }
}
