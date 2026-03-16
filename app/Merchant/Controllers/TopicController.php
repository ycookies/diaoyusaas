<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers;

use App\Models\Hotel\Topic;
use App\Models\Hotel\TopicType;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Support\Str;

// 专题列表
class TopicController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('专题列表')
            ->description('全部')
            ->breadcrumb(['text'=>'专题列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Topic::with('type'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id');
            $grid->column('title_as','文章ID');
            $grid->column('type.name','所属分类');
            //$grid->column('click');
            $grid->column('title');
            /*$grid->column('contents');
            $grid->column('writer');
            $grid->column('keywords');
            $grid->column('seotitle');
            $grid->column('description');
            $grid->column('tuijian');
            $grid->column('status');*/
            $grid->column('created_at');
            $grid->tools('<a class="btn btn-white btn-outline" target="_blank" href="' . admin_url('topic-type') . '">管理分类</a>');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
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
        return Show::make($id, new Topic(), function (Show $show) {
            $show->field('id');
            //$show->field('hotel_id');
            $show->field('user_id');
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
        return Form::make(new Topic(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('user_id')->value(Admin::user()->id);
            $form->select('type_id','所属专题分类')->options(TopicType::where(['hotel_id'=> Admin::user()->hotel_id])->pluck('name','id'))->required();
            //$form->text('click');
            $form->text('title','专题标题')->required();
            $form->hidden('title_as','英文别称')->default(Str::random(8));
            $form->editor('contents','文章内容')->required();
            //$form->text('tuijian');
            //$form->text('writer');
            //$form->tags('keywords')->help('最多支持5个标签');
            //$form->hidden('seotitle');
            //$form->text('description');
            //$form->hidden('status')->value(1);
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
