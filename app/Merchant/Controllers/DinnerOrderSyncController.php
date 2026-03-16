<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers;

use App\Models\Hotel\DinnerOrderSync;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DinnerOrderSyncController extends AdminController
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
        return Grid::make(new DinnerOrderSync(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');

            $grid->column('id')->sortable();
            $grid->column('trade_no');
            $grid->column('hotel_id');
            $grid->column('image_material_id');
            $grid->column('order_data');
            $grid->column('order_id');
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
        return Show::make($id, new DinnerOrderSync(), function (Show $show) {
            $show->field('id');
            $show->field('trade_no');
            $show->field('hotel_id');
            $show->field('image_material_id');
            $show->field('order_data');
            $show->field('order_id');
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
        return Form::make(new DinnerOrderSync(), function (Form $form) {
            $form->display('id');
            $form->text('trade_no');
            $form->text('hotel_id');
            $form->text('image_material_id');
            $form->text('order_data');
            $form->text('order_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
