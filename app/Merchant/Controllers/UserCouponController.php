<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\Usercoupon;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

class UsercouponController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('管理会员优惠券')
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
        return Grid::make(Usercoupon::with('user','hotel','coupon'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('id');
            $grid->column('user.nick_name','用户')->link(function ($value) {
                return admin_url('/user-member?_search_='.$this->user_id);
            });
            $grid->column('coupon.name','优惠券')->link(function ($value) {
                return admin_url('/coupon?_search_='.$this->coupon_id);
            });
            $grid->column('sy_time','使用时间')
                ->if(function () {
                return $this->sy_time == '';
            })
                ->display('未使用');
            $grid->column('expire_time','过期时间');
            $grid->column('created_at');
            $grid->quickSearch(['user_id'])->placeholder('用户ID');
            $grid->disableBatchDelete();
            //$grid->disableActions();
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                //
                $actions->disableView();
            });
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
        return Show::make($id, new Usercoupon(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('coupons_id');
            $show->field('state');
            $show->field('time');
            $show->field('sy_time');
            $show->field('uniacid');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Usercoupon(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('coupons_id');
            $form->text('state');
            $form->text('time');
            $form->text('sy_time');
            $form->text('uniacid');
        });
    }
}
