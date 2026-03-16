<?php

namespace App\Admin\Controllers;

use App\Models\PortalMenu;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PortalMenuController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new PortalMenu(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('parent_id');
            $grid->column('order');
            $grid->column('title');
            $grid->column('icon');
            $grid->column('uri');
            $grid->column('extension');
            $grid->column('show');
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
        return Show::make($id, new PortalMenu(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('order');
            $show->field('title');
            $show->field('icon');
            $show->field('uri');
            $show->field('extension');
            $show->field('show');
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
        return Form::make(new PortalMenu(), function (Form $form) {
            $form->display('id');
            $form->text('parent_id');
            $form->text('order');
            $form->text('title');
            $form->text('icon');
            $form->text('uri');
            $form->text('extension');
            $form->text('show');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
