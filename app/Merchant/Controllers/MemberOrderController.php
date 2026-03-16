<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\MemberOrder;
use App\Models\Hotel\MemberVipSet;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Form\NestedForm;

class MemberOrderController extends AdminController
{

     /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('购买会员卡订单')
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
        return Grid::make(MemberOrder::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('user.name','用户');
            $grid->column('order_no');
            $grid->column('pay_price');
            //$grid->column('pay_type');
            $grid->column('pay_status','是否支付')->bool();
            $grid->column('pay_time');
            //$grid->column('detail');
            //$grid->column('is_delete');
            $grid->column('created_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id');
        
            });
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->disableColumnSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
            $grid->wrap(function (Renderable $view){
                $tab = Tab::make();
                $tab->addLink('超级VIP会员卡',admin_url('member-vip'));
                $tab->add('购买订单', $view,true);

                return $tab;
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
        return Show::make($id,MemberOrder::with('user','vipCard'), function (Show $show) {
            $show->field('id');
            $show->field('user.name','用户');
            //$show->field('mall_id');
            $show->field('order_no');
            $show->field('pay_price');
            $show->field('pay_type');
            $show->field('pay_status');
            $show->field('pay_time');
            $show->field('detail');
            //$show->field('vipId');
            $show->field('created_at');
            //$show->field('updated_at');
            $show->relation('vipCard','会员卡信息', function ($model) {
                return Show::make($model->vipId, new MemberVipSet(), function (Show $show) {
                    // 设置路由
                    //$show->setResource('/users');
                    $show->id();
                    $show->name();
                    $show->field('pic_url','会员图标')->image();
                    $show->field('bg_pic_url','背景图片')->image();
                    $show->field('vip_days','有效天数');
                    $show->field('price','购买价格');
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return false;
        return Form::make(new MemberOrder(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('mall_id');
            $form->text('order_no');
            $form->text('pay_price');
            $form->text('pay_type');
            $form->text('pay_status');
            $form->text('pay_time');
            $form->text('detail');
            $form->text('is_delete');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
