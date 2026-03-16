<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\SubscribeDeliveryGood;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 住中服务 预约送物

class SubscribeDeliveryGoodController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('预约送物')
            ->description('全部')
            ->breadcrumb(['text'=>'预约送物','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SubscribeDeliveryGood(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])
                ->orderBy('id','DESC');
            /*$grid->column('id')->sortable();
            $grid->column('seller_id');*/
            $grid->column('goods_name');
            $grid->column('goods_img');
            $grid->column('number');
            $grid->column('desc');
            $grid->column('sales_volume');
            $grid->column('recommend');
            $grid->column('putaway');
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
        return Show::make($id, new SubscribeDeliveryGood(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('goods_name');
            $show->field('goods_img');
            $show->field('number');
            $show->field('desc');
            $show->field('sales_volume');
            $show->field('recommend');
            $show->field('putaway');
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
        return Form::make(new SubscribeDeliveryGood(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('goods_name');
            $form->text('goods_img');
            $form->text('number');
            $form->text('desc');
            $form->text('sales_volume');
            $form->text('recommend');
            $form->text('putaway');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
