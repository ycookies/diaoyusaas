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

use App\Models\Hotel\ProfitsharingReceiver;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Hotel;
use App\Services\BookingOrderService;
// 列表
class ProfitsharingReceiverController extends AdminController
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
        return Grid::make(ProfitsharingReceiver::with('hotel'), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id')->sortable();
            $grid->column('hotel.name',' 酒店');
            $grid->column('relation_type','关系')->using(ProfitsharingReceiver::Relation_type_arr);
            $grid->column('receiver_uid');
            $grid->column('type')->using(ProfitsharingReceiver::Type_arr);
            $grid->column('account','账号')->help('商户号,或个人open_id');
            $grid->column('name');
            $grid->column('rate')->append('%');
            $grid->column('status')->switch();
            $grid->column('created_at');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
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
        return Show::make($id, new ProfitsharingReceiver(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('relation_type');
            $show->field('receiver_uid');
            $show->field('type');
            $show->field('account');
            $show->field('name');
            $show->field('rate');
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
        return Form::make(new ProfitsharingReceiver(), function (Form $form) {
            $form->display('id');
            $form->select('hotel_id')->options(Hotel::where([['id','<>',1],['shop_open','=',1]])->pluck('name', 'id'))->required();
            $form->select('relation_type')->options(ProfitsharingReceiver::Relation_type_arr)->required();
            $form->radio('type')->options(ProfitsharingReceiver::Type_arr)->required();
            $form->text('receiver_uid')->default(1)->required();
            $form->text('account')->help('商户号,或个人open_id')->required();
            $form->text('name')->help('必须是全称')->required();
            $form->rate('rate')->help('百分比')->required();
            //$form->text('status');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
