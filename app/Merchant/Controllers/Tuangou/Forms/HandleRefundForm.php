<?php

namespace App\Merchant\Controllers\Tuangou\Forms;


use App\Models\Hotel\OrderRefund;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Services\OrderService;
use Dcat\Admin\Admin;

class HandleRefundForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input) {
        if (empty($input['refund_handle'])) {
            return JsonResponse::make()->error('请选择处理方式');
        }
        $refund_handle = $input['refund_handle'];
        $order_no = $input['order_no'];
        $orderRefund = OrderRefund::where(['hotel_id'=> Admin::user()->hotel_id,'order_no' =>$order_no])->first();
        if(!$orderRefund){
            return JsonResponse::make()->error('找不到退款订单信息');
        }
        if($refund_handle == 2){
            $service = new \App\Services\OrderService();
            $status = $service->fullOrderRefund($orderRefund->hotel_id,$orderRefund->order_no,$orderRefund->refund_desc);
            if($status === true){
                return JsonResponse::make()->data([])->success('处理成功，已成功退款')->refresh();
            }

            return JsonResponse::make()->error('退款失败.原因：'.$status);
        }
        // 更新退款订单状态
        OrderRefund::upStatus($orderRefund->order_no,$orderRefund::Status3);

        return JsonResponse::make()->success('处理成功，已拒绝退款')->refresh();
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {
        $payload     = $this->payload;
        $orderRefund = OrderRefund::where(['order_no' => $payload['order_no']])->first();
        $this->hidden('hotel_id')->value($orderRefund->hotel_id);
        $this->text('order_no', '订单号')->value($orderRefund->order_no)->readOnly()->required();
        $this->text('out_request_no', '退款单号')->value($orderRefund->out_request_no)->readOnly()->required();
        if ($orderRefund->status == 2) {
            $this->text('order_no', '退款时间')->value($orderRefund->refund_time)->readOnly()->required();
        }

        //$this->text('user_name', '用户')->value()->readOnly()->required();
        $this->text('total_pay_price', '退款金额')->value($payload['total_pay_price'])->readOnly()->required();
        $this->text('refund_desc', '退款原因')->value($orderRefund->refund_desc)->readOnly()->required();
        $this->radio('refund_handle', '处理方式')->value($orderRefund->status)->options(['2' => '同意退款', '3' => '拒绝退款'])->required();
        if ($orderRefund->status != 1) {
            $this->disableSubmitButton();
        }
        $this->disableResetButton();
    }

}
