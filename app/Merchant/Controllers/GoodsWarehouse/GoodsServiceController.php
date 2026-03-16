<?php
namespace App\Merchant\Controllers\GoodsWarehouse;

use App\Models\Hotel\Goods\GoodsService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class GoodsServiceController extends AdminController
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
        return Grid::make(new GoodsService(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('name');
            $grid->column('remark');
            $grid->column('sort');
            $grid->column('is_default');
            $grid->column('is_delete');
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
        return Show::make($id, new GoodsService(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('name');
            $show->field('remark');
            $show->field('sort');
            $show->field('is_default');
            $show->field('is_delete');
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
        return Form::make(new GoodsService(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('name');
            $form->text('remark');
            $form->text('sort');
            $form->text('is_default');
            $form->text('is_delete');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
