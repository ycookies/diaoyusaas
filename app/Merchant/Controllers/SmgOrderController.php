<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\SmgOrder;
use App\Models\Hotel\SmgOrder as SmgOrderModels;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
class SmgOrderController extends AdminController
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
        return Grid::make(new SmgOrder(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('out_trade_no','订单号');
            //$grid->column('trade_no');
            $grid->column('uid');
            $grid->column('total_price');
            $grid->column('actual_payment');
            $grid->column('pickup_code');
            $grid->column('refund_status')->help('0未退款,1退款中，2同意退款，3拒接退款');
            $grid->column('receiving_way')->help('1送货到房间，2前台自提');
            $grid->column('room_number');
            $grid->column('consignee_name');
            $grid->column('consignee_mobile');
            $grid->column('remark');
            //$grid->column('pay_time');
            $grid->column('status')->using(SmgOrderModels::$status_arr)->label()->help('1待支付,2待收货,3已完成,4退款中,5退款完成,6取消');
            /*$grid->column('appointment_time');
            $grid->column('complete_time');
            $grid->column('refund_time');
            $grid->column('time');*/
            $grid->column('type')->help('1支付宝,2微信');
            /*
            $grid->column('sub_appid');
            $grid->column('sub_mch_id');
            $grid->column('prepay_id');*/
            $grid->column('created_at');
            $grid->quickSearch(['out_trade_no'])->placeholder('订单号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->export();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
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
        return Show::make($id, new SmgOrder(), function (Show $show) {
            $show->field('id');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('hotel_id');
            $show->field('uid');
            $show->field('total_price');
            $show->field('actual_payment');
            $show->field('status');
            $show->field('pickup_code');
            $show->field('refund_status');
            $show->field('receiving_way');
            $show->field('room_number');
            $show->field('consignee_name');
            $show->field('consignee_mobile');
            $show->field('remark');
            $show->field('pay_time');
            $show->field('appointment_time');
            $show->field('complete_time');
            $show->field('refund_time');
            $show->field('time');
            $show->field('type');
            $show->field('sub_appid');
            $show->field('sub_mch_id');
            $show->field('prepay_id');
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
        return Form::make(new SmgOrder(), function (Form $form) {
            $form->display('id');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('hotel_id');
            $form->text('uid');
            $form->text('total_price');
            $form->text('actual_payment');
            $form->text('status');
            $form->text('pickup_code');
            $form->text('refund_status');
            $form->text('receiving_way');
            $form->text('room_number');
            $form->text('consignee_name');
            $form->text('consignee_mobile');
            $form->text('remark');
            $form->text('pay_time');
            $form->text('appointment_time');
            $form->text('complete_time');
            $form->text('refund_time');
            $form->text('time');
            $form->text('type');
            $form->text('sub_appid');
            $form->text('sub_mch_id');
            $form->text('prepay_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
