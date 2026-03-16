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

use App\Models\Hotel\DinnerOrderPayRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DinnerOrderPayRecordController extends AdminController
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
        return Grid::make(new DinnerOrderPayRecord(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');

            $grid->column('id')->sortable();
            $grid->column('order_id');
            $grid->column('hotel_id');
            $grid->column('restaurant_id');
            $grid->column('price');
            $grid->column('type');
            $grid->column('write_type');
            $grid->column('pay_status');
            $grid->column('refund_status');
            $grid->column('desk_number');
            $grid->column('remark');
            $grid->column('time');
            $grid->column('status');
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
        return Show::make($id, new DinnerOrderPayRecord(), function (Show $show) {
            $show->field('id');
            $show->field('order_id');
            $show->field('hotel_id');
            $show->field('restaurant_id');
            $show->field('price');
            $show->field('type');
            $show->field('write_type');
            $show->field('pay_status');
            $show->field('refund_status');
            $show->field('desk_number');
            $show->field('remark');
            $show->field('time');
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
        return Form::make(new DinnerOrderPayRecord(), function (Form $form) {
            $form->display('id');
            $form->text('order_id');
            $form->text('hotel_id');
            $form->text('restaurant_id');
            $form->text('price');
            $form->text('type');
            $form->text('write_type');
            $form->text('pay_status');
            $form->text('refund_status');
            $form->text('desk_number');
            $form->text('remark');
            $form->text('time');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
