<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\DinnerGoodsCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 商品分类
class DinnerGoodsCategoryController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品分类')
            ->description('全部')
            ->breadcrumb(['text'=>'商品分类','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new DinnerGoodsCategory(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            //$grid->column('seller_id');
            //$grid->column('restaurant_id');
            $grid->column('icon')->image();
            $grid->column('title');
            $grid->column('pid');
            $grid->column('level');
            //$grid->column('desc');
            $grid->column('sort')->editable();
            $grid->column('status')->switch();
            $grid->column('created_at');
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
        return Show::make($id, new DinnerGoodsCategory(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('restaurant_id');
            $show->field('icon');
            $show->field('title');
            $show->field('pid');
            $show->field('level');
            $show->field('desc');
            $show->field('sort');
            $show->field('status');
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
        return Form::make(new DinnerGoodsCategory(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('restaurant_id');
            $form->text('icon');
            $form->text('title');
            $form->text('pid');
            $form->text('level');
            $form->text('desc');
            $form->text('sort');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
