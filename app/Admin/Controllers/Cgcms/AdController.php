<?php

namespace App\Admin\Controllers\Cgcms;

use App\Admin\Repositories\Cgcms\Ad;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class AdController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('广告管理')
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
        return Grid::make(new Ad(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('title');
            $grid->column('media_type');
            $grid->column('litpic')->image('','100');
            $grid->column('status')->switch();
            /*$grid->column('pid');
            $grid->column('links');
            /*$grid->column('litpic');
            $grid->column('start_time');
            $grid->column('end_time');
            $grid->column('intro');
            $grid->column('link_man');
            $grid->column('link_email');
            $grid->column('link_phone');
            $grid->column('click');
            $grid->column('bgcolor');*/

            /*$grid->column('sort_order');
            $grid->column('target');
            $grid->column('admin_id');
            $grid->column('is_del');*/
            //$grid->column('lang');
            $grid->column('created_at');
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
        return Show::make($id, new Ad(), function (Show $show) {
            $show->field('id');
            $show->field('pid');
            $show->field('media_type');
            $show->field('title');
            $show->field('links');
            $show->field('litpic')->image();
            $show->field('start_time');
            $show->field('end_time');
            $show->field('intro');
            $show->field('link_man');
            $show->field('link_email');
            $show->field('link_phone');
            $show->field('click');
            $show->field('bgcolor');
            $show->field('status');
            $show->field('sort_order');
            $show->field('target');
            $show->field('admin_id');
            $show->field('is_del');
            $show->field('lang');
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
        return Form::make(new Ad(), function (Form $form) {
            $form->display('id');
            $form->text('pid');
            $form->text('media_type');
            $form->text('title')->required();
            $form->text('links')->required();
            $form->image('litpic')->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->autoUpload()->accept('jpg,png,gif,jpeg', 'image/*')->help('尺寸：1024*600')->required();
            //$form->text('start_time');
            //$form->text('end_time');
            $form->text('intro');
            //$form->text('link_man');
            //$form->text('link_email');
            //$form->text('link_phone');
            //$form->text('click');
            //$form->text('bgcolor');
            $form->hidden('status');
            //$form->text('sort_order');
            $form->switch('target');
            $form->hidden('admin_id')->value(Admin::guard()->user()->id);
            //$form->text('is_del');
            //$form->text('lang');
            //$form->text('add_time');
            //$form->text('update_time');
        });
    }
}
