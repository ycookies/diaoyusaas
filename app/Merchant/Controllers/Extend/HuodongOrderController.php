<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers\Extend;

use App\Models\Hotel\HuodongOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class HuodongOrderController extends AdminController
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
        return Grid::make(HuodongOrder::with('user','huodong'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('user_id');
            $grid->column('hd_id');
            $grid->column('order_no');
            $grid->column('trade_no');
            $grid->column('pay_amount');
            $grid->column('pay_status');
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
        return Show::make($id, new HuodongOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('user_id');
            $show->field('hd_id');
            $show->field('order_no');
            $show->field('trade_no');
            $show->field('pay_amount');
            $show->field('pay_status');
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
        return Form::make(new HuodongOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('user_id');
            $form->text('hd_id');
            $form->text('order_no');
            $form->text('trade_no');
            $form->text('pay_amount');
            $form->text('pay_status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
