<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\HomeNav;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use App\Merchant\Renderable\MinappPagesTable;
use App\Models\Hotel\MiniprogramPage;
use Dcat\Admin\Widgets\Alert;

class HomeNavController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid =  Grid::make(new HomeNav(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','ASC');
            $grid->column('icon_url')->image('','64','64');
            $grid->column('name');
            $grid->column('url');
            $grid->column('sort')->sortable()->editable()->help('数值越小越靠前');
            $grid->column('status','展示状态')->switch();
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });

        $htmll = <<<HTML
<ol>
    <li>默认四个菜单图标为一栏,请注意!</li>
</ol>
HTML;
        $alert = Alert::make($htmll, '提示:')->info();
        return $alert.$grid;
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
        return Show::make($id, new HomeNav(), function (Show $show) {
            $show->field('id');
            $show->field('seller_id');
            $show->field('name');
            $show->field('url');
            $show->field('open_type');
            $show->field('icon_url');
            $show->field('sort');
            $show->field('status');
            $show->field('is_delete');
            $show->field('params');
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
        return Form::make(new HomeNav(), function (Form $form) {
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            $form->text('name')->required();
            $form->selectTable('url', '导航链接')
                        ->title('选择链接')
                        ->from(MinappPagesTable::make())->required()
                        ->model(MiniprogramPage::class, 'path', 'path');
            $form->text('params','导航链接参数');
            $form->iconimg('icon_url','导航图标')
                        ->disk('hotel_'.Admin::user()->hotel_id)
                        ->accept('jpg,png,jpeg')
                        ->help('图标尺寸:64*64,格式：jpg,png,jpeg')
                        ->nametype('datetime')
                        ->saveFullUrl(true)
                        ->remove(true)->required();

            // $form->image('icon_url')->rules(function (Form $form) {
            //     return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            // })->disk('admin')->accept('jpg,png,gif,jpeg', 'image/*')->autoUpload()->required();

            $form->hidden('sort');
            $form->switch('status','启用')->help('启用后，在首页展示');
            //$form->text('is_delete');
            
        
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
