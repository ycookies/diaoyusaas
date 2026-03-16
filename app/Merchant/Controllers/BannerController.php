<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Banner;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use App\Merchant\Renderable\ArtTable;
use App\Models\Hotel\Article;

class BannerController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Banner(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('pic_url')->image('','100px','60px')->width('100px');
            $grid->column('title');
            $grid->column('page_url')
                ->display(function (){
                    if($this->is_link){
                        return $this->page_url;
                    }else{
                        return '无';
                    }
                });
            $grid->column('is_link','跳转链接')->bool();
            $grid->column('is_active','激活展示')->switch();
            $grid->column('sorts','排序')->help('数值越少越靠前')->sortable()->editable();
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('title');
        
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
        return Show::make($id, new Banner(), function (Show $show) {
            $show->field('id');
            //$show->field('seller_id');
            $show->field('pic_url');
            $show->field('title');
            $show->field('page_url');
            $show->field('open_type');
            $show->field('params');
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
        return Form::make(new Banner(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            $form->image('pic_url')
                ->disk('admin')->width(3)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')
                //->dimensions(['width' => 1030, 'height' => 686])
                ->disk('oss')
                ->help('建议尺寸:1030*686')->saveFullUrl()->removable(false)->autoSave(false)->autoUpload()->required();

            /*$form->image('pic_url')->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg', 'image/*')->saveFullUrl()->url('/upload/imgs')->autoUpload()->removable(false)->autoSave(false)->required();
            */$form->text('title')->required();
            $form->radio('is_link','跳转链接')
                ->when('1',function (Form $form){
                    $form->text('page_url')->default('/pages2/article/detail');
                    $form->selectTable('params', '选择链接')
                        ->title('选择文章链接')
                        ->from(ArtTable::make())
                        ->model(Article::class, 'id', 'title');
                })
                ->options(['1' => '是','0'=>'不是'])
                ->help('如不需要跳转链接,请关闭')->default(1);

            $form->switch('is_active','激活展示')->default(1);
            $form->hidden('sorts');
            //$form->hidden('open_type');
            //$form->text('params');
            //$form->text('is_delete');
        });
    }
}
