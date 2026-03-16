<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\SubscribeDeliveryOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;


class SubscribeDeliveryOrderController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SubscribeDeliveryOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('uid');
            $grid->column('order_code');
            $grid->column('out_trade_no');
            $grid->column('trade_no');
            $grid->column('seller_id');
            $grid->column('status');
            $grid->column('pickup_code');
            $grid->column('refund_status');
            $grid->column('receiving_way');
            $grid->column('room_number');
            $grid->column('consignee_name');
            $grid->column('consignee_mobile');
            $grid->column('remark');
            $grid->column('complete_time');
            $grid->column('time');
            $grid->column('appointment_time');
            $grid->column('id_del');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new SubscribeDeliveryOrder(), function (Show $show) {
            $show->field('id');
            $show->field('uid');
            $show->field('order_code');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('seller_id');
            $show->field('status');
            $show->field('pickup_code');
            $show->field('refund_status');
            $show->field('receiving_way');
            $show->field('room_number');
            $show->field('consignee_name');
            $show->field('consignee_mobile');
            $show->field('remark');
            $show->field('complete_time');
            $show->field('time');
            $show->field('appointment_time');
            $show->field('id_del');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new SubscribeDeliveryOrder(), function (Form $form) {
            $form->display('id');
            $form->text('uid');
            $form->text('order_code');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('seller_id');
            $form->text('status');
            $form->text('pickup_code');
            $form->text('refund_status');
            $form->text('receiving_way');
            $form->text('room_number');
            $form->text('consignee_name');
            $form->text('consignee_mobile');
            $form->text('remark');
            $form->text('complete_time');
            $form->text('time');
            $form->text('appointment_time');
            $form->text('id_del');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
