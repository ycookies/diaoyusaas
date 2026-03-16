<?php

namespace App\Portal\Controllers;

use App\Models\CaseAuthtrustLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CaseAuthtrustLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CaseAuthtrustLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('batch_no');
            $grid->column('case_sysno');
            $grid->column('trust_remarks');
            $grid->column('trust_msg');
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
        return Show::make($id, new CaseAuthtrustLog(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('batch_no');
            $show->field('case_sysno');
            $show->field('trust_remarks');
            $show->field('trust_msg');
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
        return Form::make(new CaseAuthtrustLog(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('batch_no');
            $form->text('case_sysno');
            $form->text('trust_remarks');
            $form->text('trust_msg');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
