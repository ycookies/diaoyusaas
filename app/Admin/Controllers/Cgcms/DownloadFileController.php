<?php

namespace App\Admin\Controllers\Cgcms;

use App\Admin\Repositories\Cgcms\DownloadFile;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DownloadFileController extends AdminController
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
        return Grid::make(new DownloadFile(), function (Grid $grid) {
            $grid->column('file_id')->sortable();
            $grid->column('aid');
            $grid->column('title');
            $grid->column('file_url');
            $grid->column('extract_code');
            $grid->column('file_size');
            $grid->column('file_ext');
            $grid->column('file_name');
            $grid->column('server_name');
            $grid->column('file_mime');
            $grid->column('uhash');
            $grid->column('md5file');
            $grid->column('is_remote');
            $grid->column('downcount');
            $grid->column('sort_order');
            $grid->column('add_time');
            $grid->column('update_time');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('file_id');
        
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
        return Show::make($id, new DownloadFile(), function (Show $show) {
            $show->field('file_id');
            $show->field('aid');
            $show->field('title');
            $show->field('file_url');
            $show->field('extract_code');
            $show->field('file_size');
            $show->field('file_ext');
            $show->field('file_name');
            $show->field('server_name');
            $show->field('file_mime');
            $show->field('uhash');
            $show->field('md5file');
            $show->field('is_remote');
            $show->field('downcount');
            $show->field('sort_order');
            $show->field('add_time');
            $show->field('update_time');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new DownloadFile(), function (Form $form) {
            $form->display('file_id');
            $form->text('aid');
            $form->text('title');
            $form->text('file_url');
            $form->text('extract_code');
            $form->text('file_size');
            $form->text('file_ext');
            $form->text('file_name');
            $form->text('server_name');
            $form->text('file_mime');
            $form->text('uhash');
            $form->text('md5file');
            $form->text('is_remote');
            $form->text('downcount');
            $form->text('sort_order');
            $form->text('add_time');
            $form->text('update_time');
        });
    }
}
