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

use App\Models\Hotel\HongbaoUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class HongbaoUserController extends AdminController
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
        return Grid::make(new HongbaoUser(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('hongbao_id');
            $grid->column('hongbao_status');
            $grid->column('hotel_id');
            $grid->column('sy_time');
            $grid->column('order_no');
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
        return Show::make($id, new HongbaoUser(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('hongbao_id');
            $show->field('hongbao_status');
            $show->field('hotel_id');
            $show->field('sy_time');
            $show->field('order_no');
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
        return Form::make(new HongbaoUser(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('hongbao_id');
            $form->text('hongbao_status');
            $form->text('hotel_id');
            $form->text('sy_time');
            $form->text('order_no');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
