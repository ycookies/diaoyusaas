<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\OffsiteFacility;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class OffsiteFacilityController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','url'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new OffsiteFacility(), function (Grid $grid) {
            $grid->number();
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id]);
            $grid->column('name');
            $grid->column('icon');
            $grid->column('description');
            $grid->column('is_free');
            $grid->column('is_show');
            $grid->column('is_recommend');
            $grid->column('sorts');
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
        return Show::make($id, new OffsiteFacility(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('name');
            $show->field('icon');
            $show->field('description');
            $show->field('is_free');
            $show->field('is_show');
            $show->field('is_recommend');
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
        return Form::make(new OffsiteFacility(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('name','设施名');
            //$form->text('icon','设施图标');
            $form->iconimg('icon','设施图标')
                ->disk('hotel_'.Admin::user()->hotel_id)
                ->accept('jpg,png,jpeg')
                ->help('图标尺寸:32*32,格式：jpg,png,jpeg')
                ->nametype('datetime')
                ->saveFullUrl(true)
                ->remove(true);
            $form->text('description','描述');
            $form->number('sorts','排序');
            $form->radio('is_free','是否免费')->options(['1'=>'免费','2'=> '收费']);
            $form->switch('is_show','是否有');
            $form->switch('is_recommend','推荐');

        });
    }
}
