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

use App\Models\Hotel\RoomSkuWhere;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class RoomSkuWhereController extends AdminController
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
        return Grid::make(new RoomSkuWhere(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('attr_name');
            $grid->column('attr_type');
            $grid->column('attr_values');
            $grid->column('sorts');
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
        return Show::make($id, new RoomSkuWhere(), function (Show $show) {
            $show->field('id');
            $show->field('attr_name');
            $show->field('attr_type');
            $show->field('attr_values');
            $show->field('sorts');
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
        return Form::make(new RoomSkuWhere(), function (Form $form) {
            $form->display('id');
            $form->text('attr_name');
            $form->text('attr_type');
            $form->text('attr_values');
            $form->text('sorts');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
