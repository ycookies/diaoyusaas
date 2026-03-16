<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CaseArticle;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CaseArticleController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CaseArticle(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('type_id');
            $grid->column('tuijian');
            $grid->column('click');
            $grid->column('title');
            //$grid->column('contents');
            //$grid->column('writer');
            //$grid->column('keywords');
            //$grid->column('seotitle');
            //$grid->column('description');
            $grid->column('status');
            $grid->column('user_id');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
            $grid->model()->orderBy('id','DESC');
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
        return Show::make($id, new CaseArticle(), function (Show $show) {
            $show->field('id');
            $show->field('type_id');
            $show->field('tuijian');
            $show->field('click');
            $show->field('title');
            $show->field('contents');
            $show->field('writer');
            $show->field('keywords');
            $show->field('seotitle');
            $show->field('description');
            $show->field('status');
            $show->field('user_id');
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
        return Form::make(new CaseArticle(), function (Form $form) {
            $form->display('id');
            $form->text('type_id');
            $form->text('tuijian');
            $form->text('click');
            $form->text('title');
            $form->text('contents');
            $form->text('writer');
            $form->text('keywords');
            $form->text('seotitle');
            $form->text('description');
            $form->text('status');
            $form->text('user_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
