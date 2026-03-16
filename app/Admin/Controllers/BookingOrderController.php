<?php

namespace App\Admin\Controllers;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\BookingOrder as BookingOrderModel;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use App\Merchant\Renderable\ProfitsharingOrderTable;
use App\Admin\Metrics\Admin\TotalBookingOrder;
use App\Admin\Metrics\Admin\TotalBookingOrderPrice;
use App\Admin\Metrics\Admin\TotalBookingOrderUser;
use App\Models\Hotel\Hotel;

class BookingOrderController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('订房订单管理')
            ->description('全部')
            ->breadcrumb(['text'=>'订房订单管理','uri'=>''])
            ->body(function (Row $row) {
                $row->column(4, new TotalBookingOrder());
                $row->column(4, new TotalBookingOrderPrice());
                $row->column(4, new TotalBookingOrderUser());
            })
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(BookingOrder::with('room','hotel', 'user'), function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');
            //$grid->column('hotel.name','酒店名称');
            //$grid->column('out_trade_no');
            /*$grid->column('room_info','订单/客房信息')->display(function ($e){
                $h1 =  '订单编号: <span class="text-gray">'.$this->out_trade_no.'</span><br/>';
                $h1 .=  '预订房型: <span class="text-gray">'.$this->room_type.'</span><br/>';
                //$h1 .= '价  格: <span class="text-gray">'.$this->room->price.'</span><br/>';
                $h1 .= '订单日期: <span class="text-gray">'.$this->created_at.'</span><br/>';
                return $h1;
            });*/
            //$grid->column('room_type');
            $grid->column('hotel_info','酒店/订单/房型')->display(function ($e){

                $h1 =  '酒店名: <span class="text-warning">'.$this->hotel->name.'</span><br/>';
                $h1 .=  '预订房型: <span class="text-gray">'.$this->room_type.'</span><br/>';
                $h1 .= '订单号: <span class="text-gray">'.$this->out_trade_no.'</span><br/>';
                return $h1;
            });

            $grid->column('booking_info','预订信息')->display(function ($e){
                $names = !empty($this->user->name) ? $this->user->nick_name:'';
                $h1 =  '下单用户: <span class="text-gray">'.$names.'</span><br/>';
                $h1 .=  '预订人: <span class="text-gray">'.$this->booking_name.'</span><br/>';
                $h1 .= '预订电话: <span class="text-gray">'.$this->booking_phone.'</span><br/>';
                $h1 .= '预订日期: <span class="text-gray">'.$this->arrival_time.' - '.$this->departure_time.'</span><br/>';
                $h1 .= '预订备注: <span class="text-gray">'.$this->remarks.'</span><br/>';
                return $h1;
            });
            /*$grid->column('user.nick_name', '下单用户');
            $grid->column('booking_name', '预定人');
            $grid->column('booking_phone', '预定人电话');*/
            $grid->column('total_cost', '支付金额')
                ->if(function () {
                    return $this->status == 4;
                })
                ->display(function ($e) {
                    return $this->total_cost;
                })
                ->expand(function () {
                    return ProfitsharingOrderTable::make()->payload(['order_no'=> $this->out_trade_no]);
                })
                ->else()
                ->display(function ($e) {
                    return $this->total_cost;
                });
            $grid->column('is_confirm', '接单状态')->bool()->help('0取消接单 1确认接单 2等待接单');
            $grid->column('status', '订单状态')->using(BookingOrder::$status_arr);
            $grid->column('created_at', '创建时间');
            $grid->quickSearch(['order_no', 'name', 'tel'])->placeholder('入住人，入住人电话，订单编号');
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
            });
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('status', '订单状态', BookingOrder::$status_arr1);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('hotel_id','酒店')
                    ->select(Hotel::where([['id','>',100]])->pluck('name','id'))->width(3);

                $filter->equal('id','订单ID')->width(3);
                $filter->like('order_no','订单编号')->width(3);
                $filter->like('trade_no','交易流水号')->width(3);
                $filter->equal('room_type','客房')->width(3);
                $filter->like('booking_name','预订人')->width(3);
                $filter->like('booking_phone','预订电话')->width(3);
                $filter->like('预订备注')->width(3);
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
        return Show::make($id, new BookingOrder(), function (Show $show) {
            $show->field('id');
            $show->field('seller_id');
            $show->field('room_id');
            $show->field('user_id');
            $show->field('coupons_id');
            $show->field('equitycard_id');
            $show->field('order_no');
            $show->field('status');
            $show->field('seller_name');
            $show->field('seller_address');
            $show->field('coordinates');
            $show->field('arrival_time');
            $show->field('departure_time');
            $show->field('dd_time');
            $show->field('price');
            $show->field('num');
            $show->field('days');
            $show->field('room_type');
            $show->field('room_logo');
            $show->field('name');
            $show->field('tel');
            $show->field('out_trade_no');
            $show->field('dis_cost');
            $show->field('yj_cost');
            $show->field('yhq_cost');
            $show->field('yyzk_cost');
            $show->field('equitycard_cost');
            $show->field('total_cost');
            $show->field('is_delete');
            $show->field('time');
            $show->field('uniacid');
            $show->field('ytyj_cost');
            $show->field('hb_cost');
            $show->field('hb_id');
            $show->field('from_id');
            $show->field('classify');
            $show->field('type');
            $show->field('code');
            $show->field('voice');
            $show->field('bed_type');
            $show->field('isReceived');
            $show->field('isSettlement');
            $show->field('order_title');
            $show->field('out_request_no');
            $show->field('out_order_no');
            $show->field('pay_type');
            $show->field('pay_method');
            $show->field('is_deposit');
            $show->field('refund_reason');
            $show->field('refund_explain');
            $show->field('is_breakfast');
            $show->field('breakfast_amount');
            $show->field('jj_time');
            $show->field('pay_time');
            $show->field('create_time');
            $show->field('update_time');
            $show->field('trade_no');
            $show->field('is_assess');
            $show->field('remarks');
            $show->field('orderStr');
            $show->field('re_time');
            $show->field('js_time');
            $show->field('yj_cost_pay');
            $show->field('dis_cost_pay');
            $show->field('dis_cost_refund_reason');
            $show->field('voucher_detail_list');
            $show->field('buyer_pay_amount');
            $show->field('total_amount');
            $show->field('user_type');
            $show->field('sub_mch_id');
            $show->field('sub_appid');
            $show->field('prepay_id');
            $show->field('fundauth_type');
            $show->field('recommend_user_id');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new BookingOrder(), function (Form $form) {
            $form->display('id');
            $form->text('seller_id');
            $form->text('room_id');
            $form->text('user_id');
            $form->text('coupons_id');
            $form->text('equitycard_id');
            $form->text('order_no');
            $form->text('status');
            $form->text('seller_name');
            $form->text('seller_address');
            $form->text('coordinates');
            $form->text('arrival_time');
            $form->text('departure_time');
            $form->text('dd_time');
            $form->text('price');
            $form->text('num');
            $form->text('days');
            $form->text('room_type');
            $form->text('room_logo');
            $form->text('name');
            $form->text('tel');
            $form->text('out_trade_no');
            $form->text('dis_cost');
            $form->text('yj_cost');
            $form->text('yhq_cost');
            $form->text('yyzk_cost');
            $form->text('equitycard_cost');
            $form->text('total_cost');
            $form->text('is_delete');
            $form->text('time');
            $form->text('uniacid');
            $form->text('ytyj_cost');
            $form->text('hb_cost');
            $form->text('hb_id');
            $form->text('from_id');
            $form->text('classify');
            $form->text('type');
            $form->text('code');
            $form->text('voice');
            $form->text('bed_type');
            $form->text('isReceived');
            $form->text('isSettlement');
            $form->text('order_title');
            $form->text('out_request_no');
            $form->text('out_order_no');
            $form->text('pay_type');
            $form->text('pay_method');
            $form->text('is_deposit');
            $form->text('refund_reason');
            $form->text('refund_explain');
            $form->text('is_breakfast');
            $form->text('breakfast_amount');
            $form->text('jj_time');
            $form->text('pay_time');
            $form->text('create_time');
            $form->text('update_time');
            $form->text('trade_no');
            $form->text('is_assess');
            $form->text('remarks');
            $form->text('orderStr');
            $form->text('re_time');
            $form->text('js_time');
            $form->text('yj_cost_pay');
            $form->text('dis_cost_pay');
            $form->text('dis_cost_refund_reason');
            $form->text('voucher_detail_list');
            $form->text('buyer_pay_amount');
            $form->text('total_amount');
            $form->text('user_type');
            $form->text('sub_mch_id');
            $form->text('sub_appid');
            $form->text('prepay_id');
            $form->text('fundauth_type');
            $form->text('recommend_user_id');
        });
    }
}
