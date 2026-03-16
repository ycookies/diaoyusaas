<?php

namespace App\Merchant\Controllers;


use App\Models\Hotel\BookingOrder;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use App\Merchant\Renderable\ProfitsharingOrderTable;
use Dcat\Admin\Widgets;
// 预订订单管理
class BookingOrderController extends AdminController {

    public $orderinfo;
    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('订单管理')
            ->description('全部')
            ->breadcrumb(['text' => '订单管理', 'uri' => ''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        return Grid::make(BookingOrder::with('room', 'user','roomsku'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('out_trade_no');
            /*$grid->column('room_info','订单/客房信息')->display(function ($e){
                $h1 =  '订单编号: <span class="text-gray">'.$this->out_trade_no.'</span><br/>';
                $h1 .=  '预订房型: <span class="text-gray">'.$this->room_type.'</span><br/>';
                //$h1 .= '价  格: <span class="text-gray">'.$this->room->price.'</span><br/>';
                $h1 .= '订单日期: <span class="text-gray">'.$this->created_at.'</span><br/>';
                return $h1;
            });*/
            $grid->column('room_type')->display(function (){
                $htmls = $this->room_type.'<br/>';
                $htmls .= !empty($this->roomsku->roomsku_title) ? $this->roomsku->roomsku_title:''.'<br/>';
                return $htmls;
            });
            $grid->column('booking_info','预订信息')->display(function ($e){
                $names = !empty($this->user->name) ? $this->user->name:'';
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
            $grid->column('num', '房间数量')->display(function ($num){
                return $num.'间';
            });
            $grid->column('price', '原价');
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
            //$grid->column('remarks');
            $grid->column('created_at');
            $grid->quickSearch(['order_no', 'booking_name', 'booking_phone'])->placeholder('订单编号,入住人，入住人电话');
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
                //$actions->disableView();
            });
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('status', '订单状态', BookingOrder::$status_arr1);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('order_no');
            });
            /*$grid->column('coupons_id');
            $grid->column('equitycard_id');*/

            /*$grid->column('status');
            $grid->column('seller_name');
            $grid->column('seller_address');
            $grid->column('coordinates');
            $grid->column('arrival_time');
            $grid->column('departure_time');
            $grid->column('dd_time');
            $grid->column('price');
            $grid->column('num');
            $grid->column('days');
            $grid->column('room_type');
            $grid->column('room_logo');
            $grid->column('name');
            $grid->column('tel');
            $grid->column('out_trade_no');
            $grid->column('dis_cost');
            $grid->column('yj_cost');
            $grid->column('yhq_cost');
            $grid->column('yyzk_cost');
            $grid->column('equitycard_cost');
            $grid->column('total_cost');
            $grid->column('is_delete');
            $grid->column('time');
            $grid->column('uniacid');
            $grid->column('ytyj_cost');
            $grid->column('hb_cost');
            $grid->column('hb_id');
            $grid->column('from_id');
            $grid->column('classify');
            $grid->column('type');
            $grid->column('code');
            $grid->column('voice');
            $grid->column('bed_type');
            $grid->column('isReceived');
            $grid->column('isSettlement');
            $grid->column('order_title');
            $grid->column('out_request_no');
            $grid->column('out_order_no');
            $grid->column('pay_type');
            $grid->column('pay_method');
            $grid->column('is_deposit');
            $grid->column('refund_reason');
            $grid->column('refund_explain');
            $grid->column('is_breakfast');
            $grid->column('breakfast_amount');
            $grid->column('jj_time');
            $grid->column('pay_time');
            $grid->column('create_time');
            $grid->column('update_time');
            $grid->column('trade_no');
            $grid->column('is_assess');
            $grid->column('remarks');
            $grid->column('orderStr');
            $grid->column('re_time');
            $grid->column('js_time');
            $grid->column('yj_cost_pay');
            $grid->column('dis_cost_pay');
            $grid->column('dis_cost_refund_reason');
            $grid->column('voucher_detail_list');
            $grid->column('buyer_pay_amount');
            $grid->column('total_amount');
            $grid->column('user_type');
            $grid->column('sub_mch_id');
            $grid->column('sub_appid');
            $grid->column('prepay_id');
            $grid->column('fundauth_type');
            $grid->column('recommend_user_id');*/

            //$grid->column('updated_at')->sortable();


        });
    }

    public function show($id, Content $content)
    {
        Admin::style(<<<CSS
        th{color:#585858 !important;}
        td{color:#9f9b9b !important;} 
CSS
        );

        $orderinfo = BookingOrder::with('room', 'user','roomsku','refund','refunds')->where(['id'=> $id,'hotel_id'=> Admin::user()->hotel_id])->first();
        $this->orderinfo = $orderinfo;

        return  $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['show'] ?? trans('admin.show'))
            ->row($this->crad1())
            ->row($this->crad2())
            ->row($this->crad3());

        if($id == '291'){

            //->row($this->crad4());
        }else{
            return $content
                ->translation($this->translation())
                ->title($this->title())
                ->description($this->description()['show'] ?? trans('admin.show'))
                ->body($this->detail($id));

        }

    }


    // 预定房型
    public function crad1(){
        $orderinfo = $this->orderinfo;
        $htmls = view('admin.booking-order-detail.progress-bar',compact('orderinfo'));
        $body = '订单号:<span class="f12 text-gray">'.$this->orderinfo->order_no.'</span> &nbsp;&nbsp;订单状态:<span class="f12 text-gray">'.BookingOrder::$status_arr[$this->orderinfo->status].'</span> &nbsp;&nbsp;支付方式:<span class="f12 text-gray">'.BookingOrder::$type_arr[$this->orderinfo->type].'</span>';
        $crad  = Widgets\Card::make($body,$htmls)->withHeaderBorder();
        return $crad->render();
    }

    // 付款信息
    public function crad2(){
        $orderinfo = $this->orderinfo;
        if($orderinfo->pay_status != 1){
            return '';
        }
        $crad  = Widgets\Card::make('付款信息',view('admin.booking-order-detail.fukuang-info',compact('orderinfo')));
        return $crad->render();
    }

    // 预定房型
    public function crad3(){
        $orderinfo = $this->orderinfo;
        $crad  = Widgets\Card::make('预订房型',view('admin.booking-order-detail.booking-room',compact('orderinfo')))->withHeaderBorder();
        return $crad->render();
    }

    // 预定人信息
    public function crad4(){
        $orderinfo = $this->orderinfo;
        $crad  = Widgets\Card::make('预订人信息',view('admin.booking-order-detail.booking-people',compact('orderinfo')))->withHeaderBorder();
        return $crad->render();
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id) {
        return Show::make($id, new BookingOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
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
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        return Form::make(new BookingOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
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

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
