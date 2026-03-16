<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\SubscribeWake;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 住中服务 预约叫醒
class SubscribeWakeController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('预约叫醒')
            ->description('全部')
            ->breadcrumb(['text'=>'预约叫醒','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SubscribeWake(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])
                ->orderBy('id','DESC');
            //$grid->column('id')->sortable();
            //$grid->column('seller_id');
            $grid->column('order_no');
            $grid->column('room_number');
            $grid->column('status');
            $grid->column('remark');
            $grid->column('wake_time');
            $grid->column('id_del');
            $grid->column('seller_remark');
            $grid->column('uniacid');
            $grid->column('uid');
            $grid->column('created_at');
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
        return Show::make($id, new SubscribeWake(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('order_no');
            $show->field('room_number');
            $show->field('status');
            $show->field('remark');
            $show->field('wake_time');
            $show->field('id_del');
            $show->field('seller_remark');
            $show->field('uniacid');
            $show->field('uid');
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
        return Form::make(new SubscribeWake(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('order_no');
            $form->text('room_number');
            $form->text('status');
            $form->text('remark');
            $form->text('wake_time');
            $form->text('id_del');
            $form->text('seller_remark');
            $form->text('uniacid');
            $form->text('uid');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
