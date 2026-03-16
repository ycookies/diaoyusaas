<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomTiaojiaLog;
use Dcat\Admin\Admin;

class RoomSkuBatchTiaojiaForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        return $this->success('保存成功');
    }

    public function default()
    {
        return [];
    }

    public function form()
    {
        $this->width(10, 2);
        $this->action('room-sku-batch-edit-save');
        $this->confirm('确认已经填好了吗?');
        $this->hidden('act_type', '操作项')->value('diy_jiejiari');
        $this->radio('batch_tiaojia_type', '节假日')
            ->when('0', function (Form $form) {
                $form->dateRange('startDate', 'endDate', '选择日期');
            })
            ->options(\App\Services\RoomSkuTiaojiaService::Batch_tiaojia_type_arr)->required();
        $list = RoomSkuPrice::with('room')->where(['hotel_id' => Admin::user()->hotel_id,'state'=> 1])->select('id','room_id','roomsku_title','roomsku_price')->get();
        $options = [];
        if(!$list->isEmpty()){
            foreach ($list as $key => $items) {
                $options[$items->id] = $items->roomsku_title.'[￥'.$items->roomsku_price.']<span class="text-muted">('.$items->room->name.')</span>';
            }
        }


        $this->checkbox('room_sku_ids', '选择房型')->options($options)
            ->canCheckAll()
            ->required();
        $this->radio('set_price', '调价方式')
            ->when('1', function (Form $form) {
                $form->currency('set_value1', '数值')->width('150');
            })
            ->when('2', function (Form $form) {
                $form->currency('set_value2', '数值')->width('150');
            })
            ->when('3', function (Form $form) {
                $form->rate('set_value3', '数值')->help('百分比')->width('150');
            })
            ->when('4', function (Form $form) {
                $form->rate('set_value4', '数值')->help('百分比')->width('150');
            })
            ->options(RoomTiaojiaLog::Set_price_type)
            ->required();
        $this->disableResetButton();
    }
}
