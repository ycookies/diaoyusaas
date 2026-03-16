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

use App\Models\Hotel\RoomSkuGift;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class RoomSkuGiftController extends AdminController
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
        return Grid::make(new RoomSkuGift(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id','ID');
            $grid->column('sku_gift_img','礼包主图')->image('','50');
            $grid->column('sku_gift_name');
            $grid->column('sku_gift_brief')->limit(20);
            //$grid->column('sku_gift_desc');
            $grid->column('sku_gift_price');
            //$grid->column('sku_gift_sorts');
            $grid->column('created_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->like('sku_gift_name')->width(3);
        
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
        return Show::make($id, new RoomSkuGift(), function (Show $show) {
            $show->field('id');
            $show->field('sku_gift_name');
            $show->field('sku_gift_brief');
            $show->field('sku_gift_desc');
            $show->field('sku_gift_price');
            $show->field('sku_gift_sorts');
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
        return Form::make(new RoomSkuGift(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('sku_gift_name')->required();
            $form->image('sku_gift_img','轮播图')->disk('public_base')->default('https://hotel.rongbaokeji.com/img/gift.png')->saveFullUrl()->autoUpload();
            $form->text('sku_gift_brief')->required();
            $form->text('sku_gift_price')->required();
            $form->editor('sku_gift_desc');
            $form->number('sku_gift_sorts');
        
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
