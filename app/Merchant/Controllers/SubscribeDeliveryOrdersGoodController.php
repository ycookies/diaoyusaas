<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\SubscribeDeliveryOrdersGood;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;


class SubscribeDeliveryOrdersGoodController extends AdminController
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
        return Grid::make(new SubscribeDeliveryOrdersGood(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('order_id');
            $grid->column('goods_id');
            $grid->column('goods_name');
            $grid->column('goods_img');
            $grid->column('number');
            $grid->column('price');
            $grid->column('total_price');
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
        return Show::make($id, new SubscribeDeliveryOrdersGood(), function (Show $show) {
            $show->field('id');
            $show->field('order_id');
            $show->field('goods_id');
            $show->field('goods_name');
            $show->field('goods_img');
            $show->field('number');
            $show->field('price');
            $show->field('total_price');
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
        return Form::make(new SubscribeDeliveryOrdersGood(), function (Form $form) {
            $form->display('id');
            $form->text('order_id');
            $form->text('goods_id');
            $form->text('goods_name');
            $form->text('goods_img');
            $form->text('number');
            $form->text('price');
            $form->text('total_price');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
