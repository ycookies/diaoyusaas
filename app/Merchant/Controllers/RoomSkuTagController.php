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

use App\Models\Hotel\RoomSkuTag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class RoomSkuTagController extends AdminController
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
        return Grid::make(new RoomSkuTag(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('id')->sortable();
            $grid->column('sku_tags_name');
            $grid->column('sku_tags_tips');
            $grid->column('sku_sorts');
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
        return Show::make($id, new RoomSkuTag(), function (Show $show) {
            $show->field('id');
            $show->field('sku_tags_name');
            $show->field('sku_tags_tips');
            $show->field('sku_sorts');
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
        return Form::make(new RoomSkuTag(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('sku_tags_name');
            $form->text('sku_tags_tips');
            $form->text('sku_sorts')->value(50);
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
