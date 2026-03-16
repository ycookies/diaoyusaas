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

use App\Models\Hotel\SmgGood;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class SmgGoodController extends AdminController
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
        return Grid::make(new SmgGood(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('recommend','DESC');
            $grid->column('id')->sortable();
            //$grid->column('hotel_id');
            $grid->column('cid');
            $grid->column('goods_name');
            $grid->column('goods_img')->image('','100px')->width('100px');
            $grid->column('price');
            $grid->column('number');
            $grid->column('desc');
            $grid->column('sales_volume');
            $grid->column('recommend')->switch();
            $grid->column('putaway')->switch();
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
        return Show::make($id, new SmgGood(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('cid');
            $show->field('goods_name');
            $show->field('goods_img');
            $show->field('price');
            $show->field('number');
            $show->field('desc');
            $show->field('sales_volume');
            $show->field('recommend');
            $show->field('putaway');
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
        return Form::make(new SmgGood(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('cid');
            $form->text('goods_name');
            $form->text('goods_img');
            $form->text('price');
            $form->text('number');
            $form->text('desc');
            $form->text('sales_volume');
            $form->text('recommend');
            $form->text('putaway');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
