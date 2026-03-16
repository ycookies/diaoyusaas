<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\ProfitsharingOrder;
use App\Models\Hotel\ProfitsharingReceiver;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Merchant\Renderable\ProfitsharingOrderTable;

// 列表
class ProfitsharingOrderController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('分账方订单')
            ->description('全部')
            ->breadcrumb(['text'=>'分账方订单','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(ProfitsharingOrder::with('hotel','order','receiver'), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id')->sortable();
            $grid->column('hotel.name','酒店名称');
            $grid->column('receiver_num','参入人数');
            //$grid->column('receiver.type','分账方类型')->using(ProfitsharingReceiver::Type_arr);
            //$grid->column('receiver.relation_type','分账关系')->using(ProfitsharingReceiver::Relation_type_arr);
            //$grid->column('receiver_uid');
            //$grid->column('rate');
            $grid->column('profitsharing_no');
            //$grid->column('transaction_id');
            $grid->column('order_no');
            $grid->column('profitsharing_receiver_order','分账明细')
                ->display('查看')
                ->expand(function () {
                    return ProfitsharingOrderTable::make()->payload(['order_no'=> $this->order_no]);
                });
            $grid->column('order_price','订单总金额');
            $grid->column('order_profitsharing_after_price','订单分账后剩余金额');
            $grid->column('profitsharing_total_price','分账支出总金额')->help('单位:元');
            $grid->column('profitsharing_status')->using(ProfitsharingOrder::Status_arr)->label(ProfitsharingOrder::Status_arr_label);
            $grid->column('created_at');
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();


            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('hotel_id','酒店ID');
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
