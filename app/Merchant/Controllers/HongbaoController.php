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

use App\Models\Hotel\Hongbao;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class HongbaoController extends AdminController
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
        return Grid::make(new Hongbao(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('user_id');
            $grid->column('grant_type');
            $grid->column('name');
            $grid->column('start_time');
            $grid->column('end_time');
            $grid->column('conditions');
            $grid->column('number');
            $grid->column('cost');
            $grid->column('need_cost');
            $grid->column('type');
            $grid->column('introduce');
            $grid->column('lq_num');
            $grid->column('klqzs');
            $grid->column('is_all');
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
        return Show::make($id, new Hongbao(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('user_id');
            $show->field('grant_type');
            $show->field('name');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('conditions');
            $show->field('number');
            $show->field('cost');
            $show->field('need_cost');
            $show->field('type');
            $show->field('introduce');
            $show->field('lq_num');
            $show->field('klqzs');
            $show->field('is_all');
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
        return Form::make(new Hongbao(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('user_id');
            $form->text('grant_type');
            $form->text('name');
            $form->text('start_time');
            $form->text('end_time');
            $form->text('conditions');
            $form->text('number');
            $form->text('cost');
            $form->text('need_cost');
            $form->text('type');
            $form->text('introduce');
            $form->text('lq_num');
            $form->text('klqzs');
            $form->text('is_all');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
