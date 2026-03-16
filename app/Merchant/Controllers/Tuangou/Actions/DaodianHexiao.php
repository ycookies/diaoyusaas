<?php

namespace App\Merchant\Controllers\Tuangou\Actions;

use App\Models\Hotel\Goods\Good;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class DaodianHexiao extends RowAction {

    /**
     * 标题
     * @return string
     */
    public function title() {

        $str = '<i class="feather icon-arrow-down tips" data-title="核销"></i>';
        return $str;
    }

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request) {
        // 获取当前行ID
        $status   = $request->get('status');
        $goods_id = $request->get('goods_id');
        if ($status == 0) {
            Good::goodsUp($goods_id);
        } else {
            Good::goodsDm($goods_id);
        }
        return $this->response()->success('操作成功')->refresh();
    }

    /**
     * @return string|void
     */
    public function confirm() {
        $msg = '现在进行到店核销吗？';
        return [$msg, '',];
    }


    /**
     * @return array
     */
    protected function parameters() {
        return [
            'order_id'   => $this->row->order_id,
        ];
    }
}
