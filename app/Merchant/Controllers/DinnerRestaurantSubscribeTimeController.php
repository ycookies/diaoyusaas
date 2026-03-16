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

use App\Models\Hotel\DinnerRestaurantSubscribeTime;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DinnerRestaurantSubscribeTimeController extends AdminController
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
        return Grid::make(new DinnerRestaurantSubscribeTime(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');

            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('restaurant_id');
            $grid->column('start_time');
            $grid->column('end_time');
            $grid->column('create_time');
            $grid->column('update_time');
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
        return Show::make($id, new DinnerRestaurantSubscribeTime(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('restaurant_id');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('create_time');
            $show->field('update_time');
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
        return Form::make(new DinnerRestaurantSubscribeTime(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('restaurant_id');
            $form->text('start_time');
            $form->text('end_time');
            $form->text('create_time');
            $form->text('update_time');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
