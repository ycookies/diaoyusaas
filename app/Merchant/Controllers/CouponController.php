<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Coupon;
use App\Models\Hotel\Coupon as CouponModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
// 优惠券管理
class CouponController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('优惠券管理')
            ->description('全部')
            ->breadcrumb(['text'=>'优惠券管理','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Coupon(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('name');
            $grid->column('grant_type')->using(CouponModel::$grant_type_arr)->help('1直接发放2收藏3分享');
            $grid->column('start_time','发放周期')->display(function ($e) {
                return $this->start_time.'<br/>'.$this->end_time;
            });
            //$grid->column('end_time');
            //$grid->column('conditions');
            $grid->column('number');
            $grid->column('cost');
            //$grid->column('need_cost');
            //$grid->column('type');
            //$grid->column('introduce');
            $grid->column('klqzs');
            //$grid->column('klqzs');
            //$grid->column('time');
            //$grid->column('uniacid');
            //$grid->column('is_all');
            //$grid->column('status')->using(CouponModel::$status_arr)->help('1启用0停用')->label();
            $grid->column('status')->switch();
            $grid->column('created_at');
            $grid->enableDialogCreate(); // 开启弹窗创建表单
            //$grid->column('updated_at')->sortable();
            $grid->quickSearch(['id','name'])->placeholder('券ID,优惠券名称');
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
        return Show::make($id, new Coupon(), function (Show $show) {
            //$show->field('id');
            //$show->field('seller_id');
            $show->field('user_id');
            $show->field('grant_type');
            $show->field('name');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('conditions');
            $show->field('number');
            $show->field('cost');
            $show->field('need_cost');
            $show->field('type');
            $show->field('introduce');
            $show->field('lq_num');
            $show->field('klqzs');
            $show->field('time');
            $show->field('uniacid');
            $show->field('is_all');
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
        return Form::make(new Coupon(), function (Form $form) {
            //$form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('user_id')->value(Admin::user()->id);
            $form->text('name')->required();
            $form->radio('grant_type','领取方式')->options(CouponModel::$grant_type_arr)->required();
            $form->datetimeRange('start_time','end_time')->required();
            $form->text('conditions');
            $form->number('number')->default('10000')->required();
            $form->currency('cost')->required();
            $form->currency('need_cost')->required();
            $form->hidden('type')->value('2');
            $form->text('introduce');
            //$form->text('lq_num');
            $form->number('klqzs');
            //$form->text('time');
            //$form->text('uniacid');
            //$form->text('is_all');
            $form->switch('status','启用状态');
            //$form->display('created_at');
            //$form->display('updated_at');
            $form->disableHeader();
            $form->disableListButton();
            $form->disableViewButton();
            $form->disableViewCheck();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
        });
    }
}
