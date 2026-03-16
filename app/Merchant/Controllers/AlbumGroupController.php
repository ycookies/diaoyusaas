<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\AlbumGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class AlbumGroupController extends AdminController
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
        return Grid::make(new AlbumGroup(), function (Grid $grid) {
            //$grid->column('id')->sortable();
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->number();
            //$grid->column('hotel_id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('sort');
            $grid->column('status')->switch();
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->enableDialogCreate();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('name')->width(3);
        
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
        return Show::make($id, new AlbumGroup(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('name');
            $show->field('description');
            $show->field('sort');
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
        return Form::make(new AlbumGroup(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('name')->required();
            $form->text('description');
            $form->text('sort');
            $form->switch('status')->default(1);
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
