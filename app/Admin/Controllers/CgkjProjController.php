<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CgkjProj;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CgkjProjController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CgkjProj(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('code');
            $grid->column('name');
            $grid->column('description');
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
        return Show::make($id, new CgkjProj(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('code');
            $show->field('name');
            $show->field('description');
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
        return Form::make(new CgkjProj(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('code');
            $form->text('name');
            $form->text('description');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
