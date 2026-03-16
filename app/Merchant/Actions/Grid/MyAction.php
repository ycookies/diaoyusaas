<?php

namespace App\Merchant\Actions\Grid;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Actions\Response;
//use Dcat\Admin\Models\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use App\Services\RoomTiaojiaService;

class MyAction extends RowAction
{
    /**
     * 标题
     *
     * @return string
     */
    public function title()
    {
        return '<i class="feather icon-delete tips" data-title="取消清除调价"></i>';
    }

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        // 获取当前行ID
        $tiaojia_logid = $this->getKey();
        $status = RoomTiaojiaService::removeTiaojia($tiaojia_logid);
        return $this->response()->success('取消成功')->refresh();
    }

    /**
     * @return string|void
     */
    public function confirm()
    {
        return [
            // 确认弹窗 title
            "您确定要取消清除这次调价吗？",
            // 确认弹窗 content
            $this->row->id,
        ];
    }


    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
