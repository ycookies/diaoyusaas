<?php
namespace App\Merchant\Controllers\GoodsWarehouse;


use App\Models\Hotel\Goods\Good;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class GoodsController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
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
        return Grid::make(new Good(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('goods_warehouse_id');
            $grid->column('status');
            $grid->column('price');
            $grid->column('use_attr');
            $grid->column('attr_groups');
            $grid->column('goods_stock');
            $grid->column('virtual_sales');
            $grid->column('confine_count');
            $grid->column('pieces');
            $grid->column('forehead');
            $grid->column('freight_id');
            $grid->column('give_integral');
            $grid->column('give_integral_type');
            $grid->column('forehead_integral');
            $grid->column('forehead_integral_type');
            $grid->column('accumulative');
            $grid->column('individual_share');
            $grid->column('attr_setting_type');
            $grid->column('is_level');
            $grid->column('is_level_alone');
            $grid->column('share_type');
            $grid->column('sign');
            $grid->column('app_share_pic');
            $grid->column('app_share_title');
            $grid->column('is_default_services');
            $grid->column('sort');
            $grid->column('is_delete');
            $grid->column('payment_people');
            $grid->column('payment_num');
            $grid->column('payment_amount');
            $grid->column('payment_order');
            $grid->column('confine_order_count');
            $grid->column('is_area_limit');
            $grid->column('area_limit');
            $grid->column('form_id');
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
        return Show::make($id, new Good(), function (Show $show) {
            $show->field('id');
            $show->field('id');
            $show->field('hotel_id');
            $show->field('goods_warehouse_id');
            $show->field('status');
            $show->field('price');
            $show->field('use_attr');
            $show->field('attr_groups');
            $show->field('goods_stock');
            $show->field('virtual_sales');
            $show->field('confine_count');
            $show->field('pieces');
            $show->field('forehead');
            $show->field('freight_id');
            $show->field('give_integral');
            $show->field('give_integral_type');
            $show->field('forehead_integral');
            $show->field('forehead_integral_type');
            $show->field('accumulative');
            $show->field('individual_share');
            $show->field('attr_setting_type');
            $show->field('is_level');
            $show->field('is_level_alone');
            $show->field('share_type');
            $show->field('sign');
            $show->field('app_share_pic');
            $show->field('app_share_title');
            $show->field('is_default_services');
            $show->field('sort');
            $show->field('is_delete');
            $show->field('payment_people');
            $show->field('payment_num');
            $show->field('payment_amount');
            $show->field('payment_order');
            $show->field('confine_order_count');
            $show->field('is_area_limit');
            $show->field('area_limit');
            $show->field('form_id');
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
        return Form::make(new Good(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('goods_warehouse_id');
            $form->text('status');
            $form->text('price');
            $form->text('use_attr');
            $form->text('attr_groups');
            $form->text('goods_stock');
            $form->text('virtual_sales');
            $form->text('confine_count');
            $form->text('pieces');
            $form->text('forehead');
            $form->text('freight_id');
            $form->text('give_integral');
            $form->text('give_integral_type');
            $form->text('forehead_integral');
            $form->text('forehead_integral_type');
            $form->text('accumulative');
            $form->text('individual_share');
            $form->text('attr_setting_type');
            $form->text('is_level');
            $form->text('is_level_alone');
            $form->text('share_type');
            $form->text('sign');
            $form->text('app_share_pic');
            $form->text('app_share_title');
            $form->text('is_default_services');
            $form->text('sort');
            $form->text('is_delete');
            $form->text('payment_people');
            $form->text('payment_num');
            $form->text('payment_amount');
            $form->text('payment_order');
            $form->text('confine_order_count');
            $form->text('is_area_limit');
            $form->text('area_limit');
            $form->text('form_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
