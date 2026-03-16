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

use App\Models\Hotel\MemberOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class MemberOrder1Controller extends AdminController
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
        return Grid::make(new MemberOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('mall_id');
            $grid->column('order_no');
            $grid->column('trade_no');
            $grid->column('pay_price');
            $grid->column('pay_type');
            $grid->column('pay_status');
            $grid->column('pay_time');
            $grid->column('vipId');
            $grid->column('vipExpire');
            $grid->column('detail');
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
        return Show::make($id, new MemberOrder(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('mall_id');
            $show->field('order_no');
            $show->field('trade_no');
            $show->field('pay_price');
            $show->field('pay_type');
            $show->field('pay_status');
            $show->field('pay_time');
            $show->field('vipId');
            $show->field('vipExpire');
            $show->field('detail');
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
        return Form::make(new MemberOrder(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('mall_id');
            $form->text('order_no');
            $form->text('trade_no');
            $form->text('pay_price');
            $form->text('pay_type');
            $form->text('pay_status');
            $form->text('pay_time');
            $form->text('vipId');
            $form->text('vipExpire');
            $form->text('detail');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
