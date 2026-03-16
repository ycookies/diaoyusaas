<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\HotelDevice;
use App\Models\Hotel\Hotel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\WxappConfig;
use App\Services\RongbaoPayService;
// 微信小程序模板管理
class WxMinappTemplateController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('微信小程序模板管理')
            ->description('全部')
            ->breadcrumb(['text'=>'模板管理','uri'=>''])
            ->body('');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(HotelDevice::with('hotel'), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel.name','所属酒店');
            $grid->column('hotel_region');
            $grid->column('device_type');
            $grid->column('device_code');
            $grid->column('device_key');
            $grid->column('status')->using(HotelDevice::Status_arr);
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->enableDialogCreate(); // 打开弹窗创建
            $grid->quickSearch(['hotel_id'])->placeholder('酒店ID');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('hotel_id');
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
        return Show::make($id, new HotelDevice(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('hotel_region');
            $show->field('device_type');
            $show->field('device_code');
            $show->field('device_key');
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
        return Form::make(new HotelDevice(), function (Form $form) {
            $form->display('id');
            $form->select('hotel_id')->options(Hotel::all()->pluck('name','id'));
            $form->text('hotel_region');
            $form->text('device_type');
            $form->text('device_code');
            $form->text('device_key');
            $form->switch('status');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
