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

use App\Models\Hotel\HotelSetting;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class HotelSettingController extends AdminController
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
        return Grid::make(new HotelSetting(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('hotel_id');
            $grid->column('group_name');
            $grid->column('field_key');
            $grid->column('field_value');
            $grid->column('field_decs');
            $grid->column('is_delete');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new HotelSetting(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('group_name');
            $show->field('field_key');
            $show->field('field_value');
            $show->field('field_decs');
            $show->field('is_delete');
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
        return Form::make(new HotelSetting(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('group_name');
            $form->text('field_key');
            $form->text('field_value');
            $form->text('field_decs');
            $form->text('is_delete');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
