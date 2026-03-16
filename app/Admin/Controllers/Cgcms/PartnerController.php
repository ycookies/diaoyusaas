<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers\Cgcms;

use App\Models\Cgcms\Partner;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class PartnerController extends AdminController
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
        return Grid::make(new Partner(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('position');
            $grid->column('sort');
            $grid->column('title');
            $grid->column('logo');
            $grid->column('link');
            $grid->column('enable');
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
        return Show::make($id, new Partner(), function (Show $show) {
            $show->field('id');
            $show->field('position');
            $show->field('sort');
            $show->field('title');
            $show->field('logo');
            $show->field('link');
            $show->field('enable');
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
        return Form::make(new Partner(), function (Form $form) {
            $form->display('id');
            $form->text('position');
            $form->text('sort');
            $form->text('title');
            $form->text('logo');
            $form->text('link');
            $form->text('enable');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
