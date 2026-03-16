<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\EquitycardOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;

class EquitycardOrderController extends AdminController
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
        return Grid::make(new EquitycardOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('hotel_id');
            $grid->column('equitycard_id');
            $grid->column('name');
            $grid->column('card_no');
            $grid->column('equitycard_attribute');
            $grid->column('order_no');
            $grid->column('out_trade_no');
            $grid->column('dis_cost');
            $grid->column('status');
            $grid->column('pay_time');
            $grid->column('type');
            $grid->column('price');
            $grid->column('total_cost');
            $grid->column('rebate');
            $grid->column('user_name');
            $grid->column('user_tel');
            $grid->column('uniacid');
            $grid->column('coupons_id');
            $grid->column('time');
            $grid->column('create_time');
            $grid->column('update_time');
            $grid->column('id_card');
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
        return Show::make($id, new EquitycardOrder(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('hotel_id');
            $show->field('equitycard_id');
            $show->field('name');
            $show->field('card_no');
            $show->field('equitycard_attribute');
            $show->field('order_no');
            $show->field('out_trade_no');
            $show->field('dis_cost');
            $show->field('status');
            $show->field('pay_time');
            $show->field('type');
            $show->field('price');
            $show->field('total_cost');
            $show->field('rebate');
            $show->field('user_name');
            $show->field('user_tel');
            $show->field('uniacid');
            $show->field('coupons_id');
            $show->field('time');
            $show->field('create_time');
            $show->field('update_time');
            $show->field('id_card');
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
        return Form::make(new EquitycardOrder(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('hotel_id');
            $form->text('equitycard_id');
            $form->text('name');
            $form->text('card_no');
            $form->text('equitycard_attribute');
            $form->text('order_no');
            $form->text('out_trade_no');
            $form->text('dis_cost');
            $form->text('status');
            $form->text('pay_time');
            $form->text('type');
            $form->text('price');
            $form->text('total_cost');
            $form->text('rebate');
            $form->text('user_name');
            $form->text('user_tel');
            $form->text('uniacid');
            $form->text('coupons_id');
            $form->text('time');
            $form->text('create_time');
            $form->text('update_time');
            $form->text('id_card');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
