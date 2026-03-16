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

use App\Models\Hotel\RoomSheshiConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class RoomSheshiController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('客房硬件设施项')
            ->description('全部')
            ->breadcrumb(['text'=>'客房硬件设施项','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new RoomSheshiConfig(), function (Grid $grid) {
            //$grid->column('id')->sortable();
            if(Admin::user()->id == 1){
                $grid->model()->orderBy('id', 'DESC');
            }else{
                $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            }
            //$grid->column('hotel_id');
            $grid->column('sheshi_name');
            $grid->column('sheshi_as');
            $grid->column('sheshi_ico')->image('','32');
            $grid->column('sheshi_item')->label();
            $grid->disableRowSelector();
            //$grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
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
        return Show::make($id, new RoomSheshiConfig(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('sheshi_name');
            $show->field('sheshi_as');
            $show->field('sheshi_ico');
            $show->field('sheshi_item');
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
        return Form::make(new RoomSheshiConfig(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('sheshi_name');
            $form->text('sheshi_as');
            $form->text('sheshi_ico');
            $form->text('sheshi_item');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
