<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\MemberRight;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;


class MemberRightController extends AdminController
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
        return Grid::make(new MemberRight(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('member_id');
            $grid->column('title');
            $grid->column('content');
            $grid->column('pic_url');
            $grid->column('is_delete');
        
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
        return Show::make($id, new MemberRight(), function (Show $show) {
            $show->field('id');
            $show->field('member_id');
            $show->field('title');
            $show->field('content');
            $show->field('pic_url');
            $show->field('is_delete');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new MemberRight(), function (Form $form) {
            $form->display('id');
            $form->text('member_id');
            $form->text('title');
            $form->text('content');
            $form->text('pic_url');
            $form->text('is_delete');
        });
    }
}
