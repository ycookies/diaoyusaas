<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\UsersInfo;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class UsersInfoController extends AdminController
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
        return Grid::make(new UsersInfo(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('avatar');
            $grid->column('platform_user_id');
            $grid->column('integral');
            $grid->column('total_integral');
            $grid->column('balance');
            $grid->column('total_balance');
            $grid->column('parent_id');
            $grid->column('is_blacklist');
            $grid->column('contact_way');
            $grid->column('remark');
            $grid->column('is_delete');
            $grid->column('junior_at');
            $grid->column('platform');
            $grid->column('temp_parent_id');
        
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
        return Show::make($id, new UsersInfo(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('avatar');
            $show->field('platform_user_id');
            $show->field('integral');
            $show->field('total_integral');
            $show->field('balance');
            $show->field('total_balance');
            $show->field('parent_id');
            $show->field('is_blacklist');
            $show->field('contact_way');
            $show->field('remark');
            $show->field('is_delete');
            $show->field('junior_at');
            $show->field('platform');
            $show->field('temp_parent_id');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new UsersInfo(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('avatar');
            $form->text('platform_user_id');
            $form->text('integral');
            $form->text('total_integral');
            $form->text('balance');
            $form->text('total_balance');
            $form->text('parent_id');
            $form->text('is_blacklist');
            $form->text('contact_way');
            $form->text('remark');
            $form->text('is_delete');
            $form->text('junior_at');
            $form->text('platform');
            $form->text('temp_parent_id');
        });
    }
}
