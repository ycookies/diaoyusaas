<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\DinnerGood;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

class DinnerGoodController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('全部')
            ->breadcrumb(['text'=>'商品管理','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(DinnerGood::with('cats'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');
            $grid->column('restaurant_id');
            $grid->column('kitchen_id');
            $grid->column('cats.title','分类');
            $grid->column('goods_name');
            $grid->column('goods_img')->image('','100px')->width('100px');
            $grid->column('price');
            $grid->column('number');
            //$grid->column('desc');
            $grid->column('sales_volume');
            $grid->column('recommend','推荐')->switch();
            $grid->column('putaway','上架')->switch();
            $grid->column('created_at');
            $grid->quickSearch(['goods_name'])->placeholder('商品名称');
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
        return Show::make($id, new DinnerGood(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('restaurant_id');
            $show->field('kitchen_id');
            $show->field('cid');
            $show->field('goods_name');
            $show->field('goods_img')->image();
            $show->field('price');
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
        return Form::make(new DinnerGood(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            $form->text('restaurant_id')->required();
            $form->text('kitchen_id')->required();
            $form->text('cid')->required();
            $form->text('goods_name')->required();
            $form->image('goods_img')->disk('admin')->removable(false)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required();
            $form->text('price');
            $form->text('number');

            //$form->text('sales_volume');
            $form->switch('recommend');
            $form->switch('putaway');
            $form->editor('desc')->default('欢迎点购')->required();
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
