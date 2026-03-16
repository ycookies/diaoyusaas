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

use App\Models\Hotel\MiniprogramPage;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class MiniprogramPageController extends AdminController
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
        return Grid::make(new MiniprogramPage(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('miniapp');
            $grid->column('hotel_id');
            $grid->column('type');
            $grid->column('name');
            $grid->column('open_type');
            $grid->column('icon');
            $grid->column('path');
            $grid->column('status');
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
        return Show::make($id, new MiniprogramPage(), function (Show $show) {
            $show->field('id');
            $show->field('miniapp');
            $show->field('hotel_id');
            $show->field('type');
            $show->field('name');
            $show->field('open_type');
            $show->field('icon');
            $show->field('path');
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
        return Form::make(new MiniprogramPage(), function (Form $form) {
            $form->display('id');
            $form->text('miniapp');
            $form->text('hotel_id');
            $form->text('type');
            $form->text('name');
            $form->text('open_type');
            $form->text('icon');
            $form->text('path');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
