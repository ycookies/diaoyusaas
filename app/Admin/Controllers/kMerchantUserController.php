<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\MerchantUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class MerchantUserController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商家账号管理')
            ->description('全部')
            ->breadcrumb(['text'=>'账号管理','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MerchantUser(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id')->sortable();
            $grid->quickSearch(['username','name'])->placeholder('账号,用户名');
            $grid->column('parent_id','上级');
            $grid->column('username','账号');
            $grid->column('name','用户名');
            //$grid->column('avatar');
            //$grid->column('remember_token');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('username','账号');
                $filter->like('name','用户名');
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
        return Show::make($id, new MerchantUser(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('username');
            $show->field('name');
            $show->field('avatar');
            $show->field('remember_token');
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
        return Form::make(new MerchantUser(), function (Form $form) {
            $form->display('id');
            $form->text('parent_id');
            $form->text('username');
            $form->text('name');
            $form->text('avatar');
            $form->text('remember_token');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
