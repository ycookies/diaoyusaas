<?php
namespace App\Merchant\Controllers\GoodsWarehouse;

use App\Models\Hotel\Goods\GoodsCat;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class GoodsCatController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品分类')
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
        return Grid::make(new GoodsCat(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id')->sortable();
            //$grid->column('hotel_id');
            //$grid->column('parent_id');
            $grid->column('name')->tree();
            $grid->column('pic_url','图标');
            $grid->column('order');
            /*$grid->column('big_pic_url');
            $grid->column('advert_pic');
            $grid->column('advert_url');
            $grid->column('status');
            $grid->column('is_delete');
            $grid->column('is_show');
            $grid->column('advert_open_type');
            $grid->column('advert_params');*/
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
        return Show::make($id, new GoodsCat(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('parent_id');
            $show->field('name');
            $show->field('pic_url');
            $show->field('sort');
            $show->field('big_pic_url');
            $show->field('advert_pic');
            $show->field('advert_url');
            $show->field('status');
            $show->field('is_delete');
            $show->field('is_show');
            $show->field('advert_open_type');
            $show->field('advert_params');
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
        return Form::make(new GoodsCat(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $model = GoodsCat::class;
            /*$form->select('parent_id', trans('admin.parent_id'))->options(function () use ($model) {
                return $model::selectOptions();
            })->saving(function ($v) {
                return (int) $v;
            });*/
            $form->select('parent_id')->options(GoodsCat::where(['hotel_id'=>Admin::user()->hotel_id])->pluck('name','id'));
            $form->text('name')->required();
            $form->image('pic_url','分类图标')->help('建议尺寸:100*100');
            $form->hidden('order')->value(50);
            $form->submitted(function (Form $form) {
                if(empty($form->input('parent_id'))){
                    $form->parent_id = 0;
                }
            });
            /*$form->text('order');
            $form->text('big_pic_url');
            $form->text('advert_pic');
            $form->text('advert_url');
            $form->text('status');
            $form->text('is_delete');
            $form->text('is_show');
            $form->text('advert_open_type');
            $form->text('advert_params');
        
            $form->display('created_at');
            $form->display('updated_at');*/
        });
    }
}
