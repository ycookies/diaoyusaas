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

use App\Models\Hotel\SmgGoodsCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class SmgGoodsCategoryController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('超市商品分类管理')
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
        return Grid::make(new SmgGoodsCategory(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');

            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('icon')->image();
            $grid->column('title');
            $grid->column('pid');
            $grid->column('level');
            $grid->column('desc');
            $grid->column('sort')->editable()->sortable();
            $grid->column('created_at');

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
        return Show::make($id, new SmgGoodsCategory(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('icon');
            $show->field('title');
            $show->field('pid');
            $show->field('level');
            $show->field('desc');
            $show->field('sort');
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
        return Form::make(new SmgGoodsCategory(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('icon');
            $form->text('title');
            $form->text('pid');
            $form->text('level');
            $form->text('desc');
            $form->text('sort');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
