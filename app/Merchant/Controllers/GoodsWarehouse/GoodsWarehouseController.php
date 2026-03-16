<?php
namespace App\Merchant\Controllers\GoodsWarehouse;

use App\Models\Hotel\Goods\GoodsWarehouse;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Goods\GoodsCat;

// 列表
class GoodsWarehouseController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
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
        return Grid::make(GoodsWarehouse::with('cats'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id')->code()->sortable();
            $grid->column('cats.name','分类')->label();
            $grid->column('main_img')->image('','100');
            $grid->column('goods_name')->limit(20);
            $grid->column('original_price');
            $grid->column('cost_price');
            /*$grid->column('pic_url');
            $grid->column('cover_pic');
            $grid->column('video_url');
            */
            $grid->column('unit');
            //$grid->column('is_delete');
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id','商品ID')->width(3);
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
        return Show::make($id, new GoodsWarehouse(), function (Show $show) {
            //$show->field('id');
            //$show->field('hotel_id');
            $show->field('name');
            $show->field('cats_id');
            $show->field('original_price');
            $show->field('cost_price');
            $show->field('pic_url')->image();
            $show->field('detail')->unescape();
            $show->field('cover_pic');
            $show->field('video_url');
            $show->field('unit');
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
        return Form::make(new  GoodsWarehouse(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);

            $model = GoodsCat::class;
            /*$form->select('cats_id', '分类')->options(function () use ($model) {
                return GoodsCat::selectOptions();
            })->saving(function ($v) {
                return (int) $v;
            })->width(3);*/

            $form->select('cats_id')->width(3)
                ->options(GoodsCat::where(['hotel_id'=>Admin::user()->hotel_id])
                    ->pluck('name','id'))->required();
            $form->text('goods_name')->required();
            $form->text('unit')->required();
            $form->currency('original_price')->required();
            $form->currency('cost_price')->required();
            $form->image('main_img','主图')->disk('oss')
                ->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')
                ->saveFullUrl()
                ->autoUpload()
                ->help('尺寸：900*600')
                ->required();
            $form->multipleImage('pic_url')->disk('oss')
                ->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')
                ->help('尺寸：900*600')
                ->saveFullUrl()
                ->autoUpload()
                ->required();;
            $form->file('video_url')->help('mp4视频文件,最大10M')->disk('oss')
                ->accept('mp4', 'video/mp4')
                ->saveFullUrl()
                ->autoUpload();

            $form->editor('detail');
            $form->hidden('is_delete')->value(0);
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
