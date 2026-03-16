<?php

namespace App\Admin\Controllers;

use App\Models\Hotel\TicketsVerificationRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class TicketsVerificationRecordController extends AdminController
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
        return Grid::make(new TicketsVerificationRecord(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('ticket_id');
            $grid->column('verifier_id');
            $grid->column('verified_at');
            $grid->column('device_info');
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
        return Show::make($id, new TicketsVerificationRecord(), function (Show $show) {
            $show->field('id');
            $show->field('ticket_id');
            $show->field('verifier_id');
            $show->field('verified_at');
            $show->field('device_info');
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
        return Form::make(new TicketsVerificationRecord(), function (Form $form) {
            $form->display('id');
            $form->text('ticket_id');
            $form->text('verifier_id');
            $form->text('verified_at');
            $form->text('device_info');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
