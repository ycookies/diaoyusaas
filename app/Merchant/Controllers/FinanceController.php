<?php

namespace App\Merchant\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use App\Models\Hotel\BookingOrder;
//use App\Merchant\Repositories\EquitycardOrder;
use App\Models\Hotel\MemberOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;

// 收入明细
class FinanceController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('收入明细')
            ->description('')
            ->breadcrumb(['text'=>'收入明细','uri'=>''])
            ->body($this->pageMain());
    }

    public function pageMain(){
        $data = [];
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('客房收入',$this->tab1(),'','booking');
        $tab->add('vip会员卡购买收入', $this->tab2(),'','vipbuy');
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    public function tab1(){
        Admin::translation('booking-order');
        return Grid::make(BookingOrder::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('user.nick_name','用户');
            $grid->column('order_no');
            $grid->column('pay_time','交易时间');
            $grid->column('arrival_time');
            $grid->column('departure_time');
            $grid->column('booking_name','预定人');
            $grid->column('booking_phone','电话');
            $grid->column('total_cost');
            $grid->column('status','状态')->using(BookingOrder::$status_arr)->help('1未付款,2已付款，3取消,4完成,5已入住,6申请退款,7退款,8拒绝退款');
            //$grid->column('remarks');
            $grid->column('created_at');
            //$grid->quickSearch(['order_no'])->placeholder('订单编号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            $grid->export();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $actions->append("<a href='/merchant/finance/booking/".$actions->row->id."' > 查看</a>");
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('order_no');
            });
        });
    }

    public function tab2(){
        return Grid::make(MemberOrder::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('user.nick_name','用户');
            $grid->column('order_no');
            $grid->column('pay_price','金额');
            //$grid->column('pay_type');
            $grid->column('pay_status','是否支付')->bool();
            $grid->column('pay_time');
            //$grid->column('detail');
            //$grid->column('is_delete');
            $grid->column('created_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id');

            });
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $actions->append("<a href='/merchant/finance/vipbuy/".$actions->row->id."' > 查看</a>");

            });
        });
    }

    public function bookingOrderDetail($id,Content $content){

        $detail =  Show::make($id, BookingOrder::with('user'), function (Show $show) {
            $show->card(function (Show\Descriptions $descrs) {
                $descrs->field('order_no','订单编号');
                $descrs->field('pay_time','交易时间');
                $descrs->field('total_cost','支付金额');
                $descrs->field('user.nick_name','用户');
                $descrs->field('booking_name','预订人');
                $descrs->field('booking_phone','预订电话');
                $descrs->field('arrival_time','入住时间');
                $descrs->field('departure_time','离店时间');
                $descrs->field('status','状态')->using(BookingOrder::$status_arr)->help('1未付款,2已付款，3取消,4完成,5已入住,6申请退款,7退款,8拒绝退款');
                $descrs->field('created_at','订单创建时间');
            });
        });

        return $content
            ->header('客房收入-详情')
            ->description('')
            ->breadcrumb(['text'=>'客房收入','url'=>'/finance#tab_booking'],['text'=>'详情','url'=>''])
            ->body($detail);
    }

    public function vipbuyDetail($id,Content $content){

        $detail =  Show::make($id, MemberOrder::with('user'), function (Show $show) {
            $show->card(function (Show\Descriptions $descrs) {
                $descrs->field('order_no','订单编号');
                $descrs->field('pay_time','交易时间');
                $descrs->field('pay_price','支付金额');
                $descrs->field('user.nick_name','用户');
                $descrs->field('pay_status','是否支付');
                $descrs->field('created_at','订单创建时间');
            });
        });

        return $content
            ->header('vip会员卡购买收入-详情')
            ->description('')
            ->breadcrumb(['text'=>'vip会员卡购买收入','url'=>'/finance#tab_vipbuy'],['text'=>'详情','url'=>''])
            ->body($detail);
    }

    public function tuangouDetail($id,Content $content){
        return $content
            ->header('团购收入')
            ->description('')
            ->breadcrumb(['text'=>'团购收入','url'=>''])
            ->body('团购收入');
    }
}
