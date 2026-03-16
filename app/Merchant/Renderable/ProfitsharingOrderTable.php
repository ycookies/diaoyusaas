<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Widgets\Table;
use Faker\Factory;
use App\Models\Hotel\ProfitsharingOrderReceiver;

class ProfitsharingOrderTable extends LazyRenderable
{
    public function render()
    {
        $order_no = $this->payload['order_no'];
        $data = [];
        $lists = ProfitsharingOrderReceiver::with('receiver')->where(['order_no'=>$order_no])->get();

        foreach ($lists as $key => $items){
            $data[] = [
                'profitsharing_no' => $items->profitsharing_no,
                'receiver_name' => $items->receiver->name,
                'profitsharing_rate' => $items->rate.'%',
                'profitsharing_price' => $items->profitsharing_price,
            ];
        }

        return Table::make(['分账单号', '分账者','分账比率', '分账金额'], $data);
    }
}
