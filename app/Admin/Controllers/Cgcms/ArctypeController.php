<?php

namespace App\Admin\Controllers\Cgcms;

use App\Admin\Repositories\Cgcms\Arctype;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tree;
use App\Models\Cgcms\Arctype as ArctypeModel;
// 列表
class ArctypeController extends AdminController
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
            ->body(function (Row $row) {
                $tree = new Tree(new ArctypeModel);
                $tree->expand(false);
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
        return Grid::make(new Arctype(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('parent_id');
            /*$grid->column('channeltype');
            $grid->column('current_channel');
            $grid->column('parent_id');
            $grid->column('topid');*/
            $grid->column('typename');
            /*$grid->column('dirname');
            $grid->column('dirpath');
            $grid->column('diy_dirpath');
            $grid->column('rulelist');
            $grid->column('ruleview');
            $grid->column('englist_name');
            $grid->column('grade');
            $grid->column('typelink');
            $grid->column('litpic');
            $grid->column('templist');
            $grid->column('tempview');
            $grid->column('seo_title');
            $grid->column('seo_keywords');
            $grid->column('seo_description');
            $grid->column('sort_order');
            $grid->column('is_hidden');
            $grid->column('is_part');
            $grid->column('admin_id');
            $grid->column('is_del');
            $grid->column('del_method');
            $grid->column('status');
            $grid->column('is_release');
            $grid->column('weapp_code');
            $grid->column('lang');
            $grid->column('add_time');
            $grid->column('update_time');
            $grid->column('target');
            $grid->column('nofollow');
            $grid->column('typearcrank');*/
        
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
        return Show::make($id, new Arctype(), function (Show $show) {
            $show->field('id');
            $show->field('channeltype');
            $show->field('current_channel');
            $show->field('parent_id');
            $show->field('topid');
            $show->field('title');
            $show->field('dirname');
            $show->field('dirpath');
            $show->field('diy_dirpath');
            $show->field('rulelist');
            $show->field('ruleview');
            $show->field('englist_name');
            $show->field('grade');
            $show->field('typelink');
            $show->field('litpic');
            $show->field('templist');
            $show->field('tempview');
            $show->field('seo_title');
            $show->field('seo_keywords');
            $show->field('seo_description');
            $show->field('sort_order');
            $show->field('is_hidden');
            $show->field('is_part');
            $show->field('admin_id');
            $show->field('is_del');
            $show->field('del_method');
            $show->field('status');
            $show->field('is_release');
            $show->field('weapp_code');
            $show->field('lang');
            $show->field('add_time');
            $show->field('update_time');
            $show->field('target');
            $show->field('nofollow');
            $show->field('typearcrank');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Arctype(), function (Form $form) {
            //$form->display('id');
            /*$form->text('channeltype');
            $form->text('current_channel');
            $form->text('topid');*/
            $form->tab('常规选项', function (Form $form) {
                $typeModel = ArctypeModel::class;
                $form->text('title')->required();
                $form->text('dirname');
                $form->text('englist_name')->required();
                $form->select('current_channel')->options(ArctypeModel::getChannelTypeList());
                $form->select('parent_id')->options(function () use ($typeModel) {
                    return $typeModel::selectOptions();
                })->saving(function ($v) {
                    return (int) $v;
                });;
                $form->switch('is_hidden');

            })->tab('高级选项', function (Form $form) {
                $form->radio('is_part')
                    ->when(1, function (Form $form) {
                        $form->text('typelink')->rules('required_if:is_part,1')->setLabelClass(['asterisk']);;
                        $form->switch('target','新窗口打开');
                        $form->switch('nofollow','nofollow(防抓取)');
                    })
                    ->options(['0'=> '内容栏目','1'=>'外部链接'])
                    ->help('外部连接（在"下方文本框"处填写网址）');
                $form->text('dirpath');
                $form->text('diy_dirpath');
                $form->text('rulelist');
                $form->text('ruleview');

                $form->text('grade');

                $form->text('litpic');
                $form->text('templist');
                $form->text('tempview')->value('view_article.htm');
                $form->text('seo_title');
                $form->text('seo_keywords');
                $form->text('seo_description');
                //$form->text('sort_order');
                $form->hidden('admin_id')->value(Admin::guard()->user()->id);
                //$form->text('is_del');
                //$form->text('del_method');
                //$form->text('status');
                $form->switch('is_release')->value(0);
                $form->hidden('weapp_code')->value('');
                $form->hidden('lang')->value('cn');
                $form->hidden('typearcrank')->value(0);
            });
        });
    }

    /**
     * @desc 单页模板管理
     * author eRic
     * dateTime 2023-02-07 10:21
     */
    public function singleEdit(){

    }
}
