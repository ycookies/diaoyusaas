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

use App\Merchant\Repositories\MemberVipSet;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class MemberVipSet1Controller extends AdminController
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
        return Grid::make(new MemberVipSet(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('mall_id');
            $grid->column('level');
            $grid->column('flag');
            $grid->column('name');
            $grid->column('auto_update');
            $grid->column('money');
            $grid->column('discount');
            $grid->column('status');
            $grid->column('pic_url');
            $grid->column('is_purchase');
            $grid->column('price');
            $grid->column('vip_days');
            $grid->column('unit');
            $grid->column('rules');
            $grid->column('is_delete');
            $grid->column('bg_pic_url');
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
        return Show::make($id, new MemberVipSet(), function (Show $show) {
            $show->field('id');
            $show->field('mall_id');
            $show->field('level');
            $show->field('flag');
            $show->field('name');
            $show->field('auto_update');
            $show->field('money');
            $show->field('discount');
            $show->field('status');
            $show->field('pic_url');
            $show->field('is_purchase');
            $show->field('price');
            $show->field('vip_days');
            $show->field('unit');
            $show->field('rules');
            $show->field('is_delete');
            $show->field('bg_pic_url');
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
        return Form::make(new MemberVipSet(), function (Form $form) {
            $form->display('id');
            $form->text('mall_id');
            $form->text('level');
            $form->text('flag');
            $form->text('name');
            $form->text('auto_update');
            $form->text('money');
            $form->text('discount');
            $form->text('status');
            $form->text('pic_url');
            $form->text('is_purchase');
            $form->text('price');
            $form->text('vip_days');
            $form->text('unit');
            $form->text('rules');
            $form->text('is_delete');
            $form->text('bg_pic_url');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
