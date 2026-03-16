<?php

namespace App\Admin\Controllers\Cgcms;

use App\Admin\Repositories\Cgcms\ImagesUpload;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class ImagesUploadController extends AdminController
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
        return Grid::make(new ImagesUpload(), function (Grid $grid) {
            $grid->column('img_id')->sortable();
            $grid->column('aid');
            $grid->column('title');
            $grid->column('image_url');
            $grid->column('intro');
            $grid->column('width');
            $grid->column('height');
            $grid->column('filesize');
            $grid->column('mime');
            $grid->column('sort_order');
            $grid->column('add_time');
            $grid->column('update_time');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('img_id');
        
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
        return Show::make($id, new ImagesUpload(), function (Show $show) {
            $show->field('img_id');
            $show->field('aid');
            $show->field('title');
            $show->field('image_url');
            $show->field('intro');
            $show->field('width');
            $show->field('height');
            $show->field('filesize');
            $show->field('mime');
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
        return Form::make(new ImagesUpload(), function (Form $form) {
            $form->display('img_id');
            $form->text('aid');
            $form->text('title');
            $form->text('image_url');
            $form->text('intro');
            $form->text('width');
            $form->text('height');
            $form->text('filesize');
            $form->text('mime');
            $form->text('sort_order');
            $form->text('add_time');
            $form->text('update_time');
        });
    }
}
