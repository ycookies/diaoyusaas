<?php

namespace App\Portal\Controllers;

use App\Models\CasePutmallLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CasePutmallLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CasePutmallLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('case_num');
            $grid->column('case_sysno');
            $grid->column('selling_price');
            $grid->column('share_rate');
            $grid->column('sell_remarks');
            $grid->column('extra_reward');
            $grid->column('extra_reward_ask');
            $grid->column('verify_remarks');
            $grid->column('verify_msg');
            $grid->column('status');
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
        return Show::make($id, new CasePutmallLog(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('case_num');
            $show->field('case_sysno');
            $show->field('selling_price');
            $show->field('share_rate');
            $show->field('sell_remarks');
            $show->field('extra_reward');
            $show->field('extra_reward_ask');
            $show->field('verify_remarks');
            $show->field('verify_msg');
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
        return Form::make(new CasePutmallLog(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('case_num');
            $form->text('case_sysno');
            $form->text('selling_price');
            $form->text('share_rate');
            $form->text('sell_remarks');
            $form->text('extra_reward');
            $form->text('extra_reward_ask');
            $form->text('verify_remarks');
            $form->text('verify_msg');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
