<?php

namespace App\Merchant\Controllers\DailyAction;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\BookingOrderClerk;
use App\Models\Hotel\Room;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\BookingOrderService;
use Dcat\Admin\Widgets\Tab;
use App\Models\Hotel\ProfitsharingOrder;
use App\Models\Hotel\ProfitsharingOrderReceiver;
use App\Models\Hotel\ProfitsharingReceiver;
use App\Models\Hotel\TicketsCode;
use App\Models\Hotel\TicketsVerificationRecord;

class HeduiDaodianController extends Controller {

    // 核对到店
    public function heduiDaodian(Content $content) {

        $htmll = <<<HTML
<ul>
    <li>1.请客人出示核销码，使用手动或扫码枪快速获取</li>
    <li>2.支持本系统的各种业务订单核销</li>
</ul>
HTML;

        $alert = Alert::make($htmll, '提示:');
        $form1 = new WidgetForm();
        $form1->text('clerk_yuan', '核销员')->width('3')->value(Admin::user()->name)->disable();
        $form1->text('checkcode_or_orderno', '核销码/订单编号')->width('4')->required();
        $form1->text('clerk_remark', '核销备注')->width('4');
        $form1->action('/hedui-daodian/check');
        $form1->confirm('现在核销到店吗？');
        //$form1->disableResetButton();
        $card1 = Card::make('', $alert->info() . $form1);

        return $content
            ->header('核对到店')
            ->description('全部')
            ->breadcrumb(['text' => '核对到店', 'uri' => ''])
            ->row($card1)
            ->row($this->OrderHexiaoHistoryRecordsList());
    }

    // 处理核销到店
    public function heduiDaodianCheck(Request $request) {
        $checkcode_or_orderno = $request->get('checkcode_or_orderno');
        $clerk_remark         = $request->get('clerk_remark');
        if (empty($checkcode_or_orderno)) {
            return returnData(405, 0, [], '核销码/订单编号 不能为空');
        }
        $where   = [];
        $where[] = ['hotel_id', '=', Admin::user()->hotel_id];
        if (preg_match('/^[a-zA-Z]/', $checkcode_or_orderno) === 1) {
            $where[] = ['order_no', '=', $checkcode_or_orderno];
        } else {
            $where[] = ['ticket_code', '=', $checkcode_or_orderno];
        }
        $detail = TicketsCode::where($where)->first();
        if (!$detail) {
            return (new WidgetForm())->response()->error('未找到订单信息');
        }
        // 已经核销
        if ($detail->status == 1) {
            return (new WidgetForm())->response()->error('已核销，请勿重复操作');
        }

        // 进行核销
        TicketsCode::verify($detail->order_no,$detail->ticket_code,Admin::user()->id,TicketsVerificationRecord::Verifiy_type_arr[2],$clerk_remark);

        // 酒店订房订单
        if($detail->sign == 'hotel_booking'){
            BookingOrder::daodian($detail->order_no,$detail->ticket_code,Admin::user()->id,BookingOrderClerk::Clerk_type_2);
            return (new WidgetForm())->response()->success('订房核对到店.操作成功')->refresh();
        }

        // 可加入其它业务逻辑

        return (new WidgetForm())->response()->success('操作成功')->refresh();

    }




    // 核销历史记录
    public function OrderHexiaoHistoryRecordsList() {
        $grid  = Grid::make(TicketsVerificationRecord::with('user','ticketsCode'), function (Grid $grid) {
            $grid->model()->where([['hotel_id', '=', Admin::user()->hotel_id]])->orderBy('id', 'DESC');
            $grid->column('id')->sortable();
            $grid->column('ticketsCode.sign', '业务名称')->using(\App\Models\Hotel\Order\Order::Sign_list);
            $grid->column('ticketsCode.order_no', '订单编号');
            $grid->column('device_info', '核销方式');
            $grid->column('user.name', '核销人');
            $grid->column('ticketsCode.status', '状态')->using(TicketsVerificationRecord::Status_arr)->label([0=>'danger',1=> 'success']);
            $grid->column('verified_remark', '核销备注');
            $grid->column('created_at', '核销日期');
            $grid->disableCreateButton();
            $grid->disableBatchActions();
            $grid->disableDeleteButton();
            $grid->disableActions();
            $grid->quickSearch(['order_no', 'verified_remark'])->placeholder('订单号,核销码,核销备注');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('ticketsCode.ticket_code','核销码')->width(3);
                $filter->like('ticketsCode.order_no','订单号')->width(3);
                $filter->like('verified_remark','备注')->width(3);
                $filter->between('created_at', '核销日期')->datetime()->width(5);
            });
        });
        $card2 = Card::make('历史核销记录', $grid);
        return $card2;
    }
    // 处理核销到店 todo 作废
    public function heduiDaodianCheck_old(Request $request) {
        $checkcode_or_orderno = $request->get('checkcode_or_orderno');
        $clerk_remark         = $request->get('clerk_remark');
        if (empty($checkcode_or_orderno)) {
            return returnData(405, 0, [], '核销码/订单编号 不能为空');
        }
        $where   = [];
        $where[] = ['hotel_id', '=', Admin::user()->hotel_id];
        if (strpos($checkcode_or_orderno, 'YXB') !== false) {
            $where[] = ['out_trade_no', '=', $checkcode_or_orderno];
        } else {
            $where[] = ['code', '=', $checkcode_or_orderno];
        }
        $detail = BookingOrder::where($where)->first();
        if (!$detail) {
            return (new WidgetForm())->response()->error('未找到订单信息');
        }
        // 已经核销
        if ($detail->voice == 2) {
            return (new WidgetForm())->response()->error('已核销，请勿重复操作');
        }
        //\DB::beginTransaction();
        //try {

        /*$model               = BookingOrder::find($detail->id);
        $model->voice        = 2;
        $model->confirm_time = date('Y-m-d H:i:s');
        $model->save();*/
        BookingOrder::daodian($detail->out_trade_no,$detail->code,Admin::user()->id,BookingOrderClerk::Clerk_type_2);

        //\DB::commit();
        return (new WidgetForm())->response()->success('操作成功')->refresh();
        /*} catch (\Error $error) {
            \DB::rollBack();
        } catch (\Exception $exception) {
            \DB::rollBack();
        }*/
        return (new WidgetForm())->response()->error('核销失败.系统异常');
    }
    // 核销历史记录 todo 作废
    public function BookingOrderClerkList() {
        $grid  = Grid::make(BookingOrderClerk::with('user', 'bookingorder'), function (Grid $grid) {
            $grid->model()->where([['hotel_id', '=', Admin::user()->hotel_id]])->orderBy('id', 'DESC');
            $grid->column('id')->sortable();
            $grid->column('bookingorder.out_trade_no', '订单编号');
            $grid->column('bookingorder.room_id', '房型')->display(function ($room_id) {
                return Room::find($room_id)->name;
            });
            $grid->column('bookingorder.booking_name', '入驻人');
            $grid->column('clerk_type', '核销方式')->using(BookingOrderClerk::Clerk_type_arr);
            $grid->column('clerk_remark', '核销备注');
            $grid->column('user.name', '核销人');
            $grid->column('created_at', '核销日期');
            $grid->disableCreateButton();
            $grid->disableBatchActions();
            $grid->disableDeleteButton();
            $grid->quickSearch(['order_no', 'clerk_remark'])->placeholder('订单号,入驻人姓名,核销备注');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('code');
                $filter->equal('order_no');
                $filter->like('clerk_remark');
            });
        });
        $card2 = Card::make('历史记录', $grid);
        return $card2;
    }

    public function ProfitsharingOrderList(){
        $grid =  Grid::make(ProfitsharingOrder::with('hotel','order','receiver'), function (Grid $grid) {
            $grid->model()->where([['hotel_id', '=', Admin::user()->hotel_id]])->orderBy('id','DESC');
            $grid->column('id')->sortable();
            $grid->column('hotel.name','酒店名称');
            $grid->column('receiver.type','分账方类型')->using(ProfitsharingReceiver::Type_arr);
            $grid->column('receiver.relation_type','分账关系')->using(ProfitsharingReceiver::Relation_type_arr);
            //$grid->column('receiver_uid');
            $grid->column('rate');
            $grid->column('profitsharing_no');
            //$grid->column('transaction_id');
            $grid->column('order_no');
            $grid->column('profitsharing_price')->help('单位:元');
            $grid->column('profitsharing_status')->using(ProfitsharingOrder::Status_arr)->label();
            $grid->column('created_at');
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('hotel_id','酒店ID');
            });
        });


        $card = Card::make('分账订单', $grid);
        return $card;
    }

    // 核销历史记录
    public function BookingOrderJiesuanLog() {
        $grid  = Grid::make(BookingOrder::class, function (Grid $grid) {
            $where = [
                ['hotel_id', '=', Admin::user()->hotel_id],
                ['status', '>=', 2]
            ];

            $grid->model()->where($where)->orderBy('id', 'DESC');
            $grid->column('out_trade_no','订单号');
            $grid->column('room_type','预定房型');
            $grid->column('user_id','下单用户');
            $grid->column('booking_name','预定人');
            $grid->column('booking_phone','预定人电话');
            $grid->column('total_cost','支付金额');
            $grid->column('is_confirm','接单状态')->using(BookingOrder::$Is_confirm)->help('0取消接单 1确认接单 2等待接单');
            $grid->column('status','订单状态')->using(BookingOrder::$status_arr);
            $grid->column('remarks','预定备注');
            $grid->column('created_at');
            $grid->quickSearch(['order_no','name','tel'])->placeholder('入住人，入住人电话，订单编号');
            $grid->disableCreateButton();
            $grid->disableBatchActions();
            $grid->disableDeleteButton();
            $grid->disableActions();
            $grid->quickSearch(['out_trade_no', 'remarks'])->placeholder('订单号,入驻人姓名,核销备注');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();
            });
        });
        $card2 = Card::make('历史记录', $grid);
        return $card2;
    }

    // 订单结算
    public function orderJiesuan(Content $content) {
        $htmll = <<<HTML
<ul>
    <li>在客人退房离店时结算,剩余资金原路返回</li>
</ul>
HTML;

        $alert = Alert::make($htmll, '结算提示:');
        $form1 = new WidgetForm();
        $form1->text('clerk_yuan', '前厅收银员')->width('3')->value(Admin::user()->name)->disable();
        $form1->text('order_no', '订单编号')->width('4')->required();
        //$form1->text('real_use_price', '实际消费金额')->width('4')->help('最多二位小数')->required();
        $form1->text('jiesuan_remark', '结算备注')->width('4');
        $form1->action('/order-jiesuan-save');
        $form1->confirm('确认现在结算吗？');
        //$form1->disableResetButton();
        $card1 = Card::make('', $alert->info() . $form1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('预订客房订单', $this->BookingOrderJiesuanLog());
        //$tab->add('结算分账订单', $this->ProfitsharingOrderList());

        return $content
            ->header('离店订单结算')
            ->description('全部')
            ->breadcrumb(['text' => '离店订单结算', 'uri' => ''])
            ->row($card1)
            ->row($tab->withCard());
    }

    // 订单结算
    public function orderJiesuanSave(Request $request) {
        $order_no = $request->get('order_no');


        $where   = [];
        $where[] = ['hotel_id', '=', Admin::user()->hotel_id];
        $where[] = ['order_no', '=', $order_no];
        $orderinfo = BookingOrder::where($where)->first();
        if (!$orderinfo) {
            return (new WidgetForm())->response()->error('未找到订单信息');
        }
        if ($orderinfo->status == 4) {
            return (new WidgetForm())->response()->error('订单已完成，请勿重复操作');
        }

        if ($orderinfo->status != 5) {
            return (new WidgetForm())->response()->error('订单不可结算,不在已入驻状态');
        }

        // 实际消费金额 大于原来的支付金额
        /*$real_use_price = $request->get('real_use_price');
        $regx      = '/^[0-9]+(.[0-9]{2})?$/'; // 最多两位小数的正整数

        // 检查数据合法性
        if (!preg_match($regx, $real_use_price)) {
            return (new WidgetForm())->response()->error('实际消费金额 最多带两位小数');
        }
        if ($real_use_price > $orderinfo->total_cost) {
            return (new WidgetForm())->response()->error('结算不能大于原支付金额');
        }*/

        /*\DB::beginTransaction();
        try {*/
            //判断通道 这里需要在表里设置 切换通道
            /*if($orderinfo->pay_method == 'weixin_pay'){

            }

            if($orderinfo->pay_method == 'weixin_auth'){

            }*/

            $status = BookingOrder::lidian($order_no);
            if($status !== true){
                return (new WidgetForm())->response()->error('结算失败');
            }

            /*\DB::commit();
            return (new WidgetForm())->response()->success('操作成功')->refresh();
        } catch (\Error $error) {
            \DB::rollBack();
        } catch (\Exception $exception) {
            \DB::rollBack();
        }*/
        return (new WidgetForm())->response()->success('操作成功')->refresh();
    }



}
