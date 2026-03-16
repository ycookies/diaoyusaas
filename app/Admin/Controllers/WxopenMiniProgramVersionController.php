<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class WxopenMiniProgramVersionController extends AdminController
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
        return Grid::make(new WxopenMiniProgramVersion(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('hotel_id');
            $grid->column('minapp_id');
            $grid->column('template_id');
            $grid->column('user_version');
            $grid->column('user_desc');
            $grid->column('ext_json');
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
        return Show::make($id, new WxopenMiniProgramVersion(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('hotel_id');
            $show->field('minapp_id');
            $show->field('template_id');
            $show->field('user_version');
            $show->field('user_desc');
            $show->field('ext_json');
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
        return Form::make(new WxopenMiniProgramVersion(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('hotel_id');
            $form->text('minapp_id');
            $form->text('template_id');
            $form->text('user_version');
            $form->text('user_desc');
            $form->text('ext_json');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
