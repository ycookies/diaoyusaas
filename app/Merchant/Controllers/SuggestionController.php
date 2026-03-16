<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Suggestion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
// 住中服务 意见箱
class SuggestionController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('意见箱')
            ->description('列表')
            ->breadcrumb(['text'=>'意见箱','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Suggestion(), function (Grid $grid) {
            //$grid->column('id')->sortable();
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])
                ->orderBy('id','DESC');
            //$grid->column('shop_id');
            //$grid->column('app_id');
            $grid->column('name');
            //$grid->column('uid');
            $grid->column('tags');
            $grid->column('content')->width(500)->display(function ($e){
                $htmls =  $this->content."<br>";
                if(!empty($this->image_path)){
                    foreach (explode(',',$this->image_path) as $key => $value) {
                        $htmls .= "<img src='$value' width='50'>";
                    }
                }
                return $htmls;
            });
            $grid->column('room_number');
            //$grid->column('image_path');
            $grid->column('reply');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            //$grid->export();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
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
        return Show::make($id, new Suggestion(), function (Show $show) {
            $show->field('id');
            $show->field('shop_id');
            $show->field('app_id');
            $show->field('name');
            $show->field('uid');
            $show->field('tags');
            $show->field('content');
            $show->field('room_number');
            $show->field('image_path');
            $show->field('reply');
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
        return Form::make(new Suggestion(), function (Form $form) {
            $form->display('id');
            $form->text('shop_id');
            $form->text('app_id');
            $form->text('name');
            $form->text('uid');
            $form->text('tags');
            $form->text('content');
            $form->text('room_number');
            $form->text('image_path');
            $form->text('reply');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
