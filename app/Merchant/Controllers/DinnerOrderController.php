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

use App\Models\Hotel\DinnerOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DinnerOrderController extends AdminController
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
        return Grid::make(new DinnerOrder(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');

            $grid->column('id')->sortable();
            $grid->column('order_code');
            $grid->column('out_trade_no');
            $grid->column('trade_no');
            $grid->column('hotel_id');
            $grid->column('restaurant_id');
            $grid->column('uid');
            $grid->column('total_price');
            $grid->column('actual_payment');
            $grid->column('status','状态');
            $grid->column('dinner_type');
            /*$grid->column('pickup_code');
            $grid->column('refund_status');
            $grid->column('receiving_way');
            $grid->column('room_number');
            $grid->column('consignee_name');
            $grid->column('consignee_mobile');
            $grid->column('remark');
            $grid->column('pay_time');
            $grid->column('complete_time');
            $grid->column('refund_time');
            $grid->column('time');
            $grid->column('appointment_time');
            $grid->column('desk_number');
            $grid->column('num');
            $grid->column('type');
            $grid->column('is_del');
            $grid->column('complete_way');
            $grid->column('source');
            $grid->column('sub_mch_id');
            $grid->column('sub_appid');
            $grid->column('prepay_id');
            $grid->column('ali_user_id');*/
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new DinnerOrder(), function (Show $show) {
            $show->field('id');
            $show->field('order_code');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('hotel_id');
            $show->field('restaurant_id');
            $show->field('uid');
            $show->field('total_price');
            $show->field('actual_payment');
            $show->field('status');
            $show->field('dinner_type');
            $show->field('pickup_code');
            $show->field('refund_status');
            $show->field('receiving_way');
            $show->field('room_number');
            $show->field('consignee_name');
            $show->field('consignee_mobile');
            $show->field('remark');
            $show->field('pay_time');
            $show->field('complete_time');
            $show->field('refund_time');
            $show->field('time');
            $show->field('appointment_time');
            $show->field('desk_number');
            $show->field('num');
            $show->field('type');
            $show->field('is_del');
            $show->field('complete_way');
            $show->field('source');
            $show->field('sub_mch_id');
            $show->field('sub_appid');
            $show->field('prepay_id');
            $show->field('ali_user_id');
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
        return Form::make(new DinnerOrder(), function (Form $form) {
            $form->display('id');
            $form->text('order_code');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('hotel_id');
            $form->text('restaurant_id');
            $form->text('uid');
            $form->text('total_price');
            $form->text('actual_payment');
            $form->text('status');
            $form->text('dinner_type');
            $form->text('pickup_code');
            $form->text('refund_status');
            $form->text('receiving_way');
            $form->text('room_number');
            $form->text('consignee_name');
            $form->text('consignee_mobile');
            $form->text('remark');
            $form->text('pay_time');
            $form->text('complete_time');
            $form->text('refund_time');
            $form->text('time');
            $form->text('appointment_time');
            $form->text('desk_number');
            $form->text('num');
            $form->text('type');
            $form->text('is_del');
            $form->text('complete_way');
            $form->text('source');
            $form->text('sub_mch_id');
            $form->text('sub_appid');
            $form->text('prepay_id');
            $form->text('ali_user_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
