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

use App\Models\Hotel\WxCardTpl;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class WxCardTplController extends AdminController
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
        return Grid::make(new WxCardTpl(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('brand_name');
            $grid->column('title');
            $grid->column('background_pic_url');
            $grid->column('logo_url');
            $grid->column('colors');
            $grid->column('notice');
            $grid->column('description');
            $grid->column('service_phone');
            $grid->column('prerogative');
            $grid->column('quantity');
            $grid->column('discount');
            $grid->column('qrcode_url');
            $grid->column('testwhitelist');
            $grid->column('form_data');
            $grid->column('response_data');
            $grid->column('card_id');
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
        return Show::make($id, new WxCardTpl(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('brand_name');
            $show->field('title');
            $show->field('background_pic_url');
            $show->field('logo_url');
            $show->field('colors');
            $show->field('notice');
            $show->field('description');
            $show->field('service_phone');
            $show->field('prerogative');
            $show->field('quantity');
            $show->field('discount');
            $show->field('qrcode_url');
            $show->field('testwhitelist');
            $show->field('form_data');
            $show->field('response_data');
            $show->field('card_id');
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
        return Form::make(new WxCardTpl(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('brand_name');
            $form->text('title');
            $form->text('background_pic_url');
            $form->text('logo_url');
            $form->text('colors');
            $form->text('notice');
            $form->text('description');
            $form->text('service_phone');
            $form->text('prerogative');
            $form->text('quantity');
            $form->text('discount');
            $form->text('qrcode_url');
            $form->text('testwhitelist');
            $form->text('form_data');
            $form->text('response_data');
            $form->text('card_id');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
