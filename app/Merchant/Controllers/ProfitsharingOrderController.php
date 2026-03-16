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

use App\Models\Hotel\ProfitsharingOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class ProfitsharingOrderController extends AdminController
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
        return Grid::make(new ProfitsharingOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('receiver_id');
            $grid->column('receiver_uid');
            $grid->column('rate');
            $grid->column('profitsharing_no');
            $grid->column('transaction_id');
            $grid->column('order_no');
            $grid->column('profitsharing_price');
            $grid->column('profitsharing_status');
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
        return Show::make($id, new ProfitsharingOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('receiver_id');
            $show->field('receiver_uid');
            $show->field('rate');
            $show->field('profitsharing_no');
            $show->field('transaction_id');
            $show->field('order_no');
            $show->field('profitsharing_price');
            $show->field('profitsharing_status');
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
        return Form::make(new ProfitsharingOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('receiver_id');
            $form->text('receiver_uid');
            $form->text('rate');
            $form->text('profitsharing_no');
            $form->text('transaction_id');
            $form->text('order_no');
            $form->text('profitsharing_price');
            $form->text('profitsharing_status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
