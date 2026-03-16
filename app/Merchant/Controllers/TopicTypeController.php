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

use App\Models\Hotel\TopicType;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tree;

// 专题列表
class TopicTypeController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('专题分类管理')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body(function (Row $row) {
                $tree = new Tree(new TopicType);
                $tree->query(function ($model) {
                    return $model->where('hotel_id', Admin::user()->hotel_id);
                });
                $tree->disableCreateButton();
                $row->column(12, $tree);
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TopicType(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('parent_id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('sort_order');
            $grid->column('depth');
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
        return Show::make($id, new TopicType(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('parent_id');
            $show->field('name');
            $show->field('description');
            $show->field('sort_order');
            $show->field('depth');
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
        return Form::make(new TopicType(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->select('parent_id','选择父级分类')->help('如不选择,默认为顶级')->options(TopicType::where(['hotel_id'=>Admin::user()->hotel_id])->pluck('name','id'))->value(0);
            $form->text('name','分类名称')->required();
            /*$form->text('description');
            $form->text('sort_order');
            $form->text('depth');

            $form->display('created_at');
            $form->display('updated_at');*/
        });
    }
}
