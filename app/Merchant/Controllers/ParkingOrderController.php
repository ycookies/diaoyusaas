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

use App\Models\Hotel\ParkingOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 停车场列表
class ParkingOrderController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('停车场缴费订单')
            ->description('全部')
            ->breadcrumb(['text'=>'停车场缴费订单','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ParkingOrder(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id');
            $grid->column('user_id');
            $grid->column('parkingNo');
            $grid->column('carNo');
            $grid->column('outTradeNo');
            $grid->column('totalAmount');
            $grid->column('transactionId');
            $grid->column('chargeTime');
            $grid->column('endChargeTime');
            $grid->column('payType');
            $grid->column('couponAmount');
            $grid->column('disAmount');
            $grid->column('payTime');
            $grid->column('mac');
            $grid->column('openid');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->quickSearch(['carNo'])->placeholder('车牌号码');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->export();
            //$grid->
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('carNo','车牌号码');
        
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
        return Show::make($id, new ParkingOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('user_id');
            $show->field('parkingNo');
            $show->field('carNo');
            $show->field('outTradeNo');
            $show->field('totalAmount');
            $show->field('transactionId');
            $show->field('chargeTime');
            $show->field('endChargeTime');
            $show->field('payType');
            $show->field('couponAmount');
            $show->field('disAmount');
            $show->field('payTime');
            $show->field('mac');
            $show->field('openid');
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
        return Form::make(new ParkingOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('user_id');
            $form->text('parkingNo');
            $form->text('carNo');
            $form->text('outTradeNo');
            $form->text('totalAmount');
            $form->text('transactionId');
            $form->text('chargeTime');
            $form->text('endChargeTime');
            $form->text('payType');
            $form->text('couponAmount');
            $form->text('disAmount');
            $form->text('payTime');
            $form->text('mac');
            $form->text('openid');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
