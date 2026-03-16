<?php

namespace App\Merchant\Controllers\Tuangou\Actions;

use App\Models\Hotel\Goods\Good;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class GoodsDown extends RowAction {

    /**
     * 标题
     * @return string
     */
    public function title() {

        if ($this->row->goods->status == 0) {
            $str = '<i class="feather icon-arrow-up tips" data-title="上架"></i>';
        } else {
            $str = '<i class="feather icon-arrow-down tips" data-title="下架"></i>';
        }
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
        $msg = '现在下架吗？';
        if ($this->row->goods->status == 0) {
            $msg = '现在上架吗？';
        }
        return [$msg, '',];
    }


    /**
     * @return array
     */
    protected function parameters() {
        return [
            'status'   => $this->row->goods->status,
            'goods_id' => $this->row->goods_id,
        ];
    }
}
