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

use App\Models\Hotel\DinnerRestaurant;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class DinnerRestaurantController extends AdminController
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
        return Grid::make(new DinnerRestaurant(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');

            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('name');
            $grid->column('ewm_logo');
            $grid->column('bq_logo');
            $grid->column('notice');
            $grid->column('img');
            $grid->column('rule');
            $grid->column('prompt');
            $grid->column('scort');
            $grid->column('service_time');
            $grid->column('state');
            $grid->column('floor');
            $grid->column('address');
            $grid->column('introduction');
            $grid->column('tel');
            $grid->column('type');
            $grid->column('star');
            $grid->column('link_tel');
            $grid->column('link_name');
            $grid->column('uniacid');
            $grid->column('create_time');
            $grid->column('update_time');
            $grid->column('pay_status');
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
        return Show::make($id, new DinnerRestaurant(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('name');
            $show->field('ewm_logo');
            $show->field('bq_logo');
            $show->field('notice');
            $show->field('img');
            $show->field('rule');
            $show->field('prompt');
            $show->field('scort');
            $show->field('service_time');
            $show->field('state');
            $show->field('floor');
            $show->field('address');
            $show->field('introduction');
            $show->field('tel');
            $show->field('type');
            $show->field('star');
            $show->field('link_tel');
            $show->field('link_name');
            $show->field('uniacid');
            $show->field('create_time');
            $show->field('update_time');
            $show->field('pay_status');
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
        return Form::make(new DinnerRestaurant(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('name');
            $form->text('ewm_logo');
            $form->text('bq_logo');
            $form->text('notice');
            $form->text('img');
            $form->text('rule');
            $form->text('prompt');
            $form->text('scort');
            $form->text('service_time');
            $form->text('state');
            $form->text('floor');
            $form->text('address');
            $form->text('introduction');
            $form->text('tel');
            $form->text('type');
            $form->text('star');
            $form->text('link_tel');
            $form->text('link_name');
            $form->text('uniacid');
            $form->text('create_time');
            $form->text('update_time');
            $form->text('pay_status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
