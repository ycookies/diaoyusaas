<?php

namespace App\Admin\Controllers;

use App\Models\Hotel\TicketsCode;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class TicketsCodeController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','url'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TicketsCode(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('order_no');
            $grid->column('ticket_code');
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
        return Show::make($id, new TicketsCode(), function (Show $show) {
            $show->field('id');
            $show->field('order_no');
            $show->field('ticket_code');
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
        return Form::make(new TicketsCode(), function (Form $form) {
            $form->display('id');
            $form->text('order_no');
            $form->text('ticket_code');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
