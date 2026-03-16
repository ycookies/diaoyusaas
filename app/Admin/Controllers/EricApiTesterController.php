<?php

namespace App\Admin\Controllers;

use App\Models\EricApiTester;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class EricApiTesterController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new EricApiTester(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('parent_id');
            $grid->column('order');
            $grid->column('title');
            $grid->column('uri');
            $grid->column('method');
            $grid->column('type');
            $grid->column('descs');
            $grid->column('head_param');
            $grid->column('api_param');
            $grid->column('resp_param');
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
        return Show::make($id, new EricApiTester(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('order');
            $show->field('title');
            $show->field('uri');
            $show->field('method');
            $show->field('type');
            $show->field('descs');
            $show->field('head_param');
            $show->field('api_param');
            $show->field('resp_param');
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
        return Form::make(new EricApiTester(), function (Form $form) {
            $form->display('id');
            $form->text('parent_id');
            $form->text('order');
            $form->text('title');
            $form->text('uri');
            $form->text('method');
            $form->text('type');
            $form->text('descs');
            $form->text('head_param');
            $form->text('api_param');
            $form->text('resp_param');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
