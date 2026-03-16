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

use App\Models\Hotel\RoomTiaojiaLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class RoomTiaojiaLogController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('客房调价日志')
            ->description('全部')
            ->breadcrumb(['text'=>'客房调价日志','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new RoomTiaojiaLog(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('seller_id');
            $grid->column('hotel_id');
            $grid->column('room_ids');
            $grid->column('date_type');
            $grid->column('start_date');
            $grid->column('end_date');
            $grid->column('set_price');
            $grid->column('set_value');
            $grid->column('status');
            $grid->column('created_at');
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
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
        return Show::make($id, new RoomTiaojiaLog(), function (Show $show) {
            $show->field('id');
            $show->field('seller_id');
            $show->field('hotel_id');
            $show->field('room_ids');
            $show->field('date_type');
            $show->field('start_date');
            $show->field('end_date');
            $show->field('set_price');
            $show->field('set_value');
            $show->field('status');
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
        return Form::make(new RoomTiaojiaLog(), function (Form $form) {
            $form->display('id');
            $form->text('seller_id');
            $form->text('hotel_id');
            $form->text('room_ids');
            $form->text('date_type');
            $form->text('start_date');
            $form->text('end_date');
            $form->text('set_price');
            $form->text('set_value');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
