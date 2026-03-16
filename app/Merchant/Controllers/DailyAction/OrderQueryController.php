<?php

namespace App\Merchant\Controllers\DailyAction;

use App\Models\Hotel\BookingOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Http\JsonResponse;
// 预订订房订单管理
class OrderQueryController extends AdminController
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
            ->body($this->grid());
    }
    protected function grid()
    {
        Admin::js(asset('js/dailywork.js'));
        return Grid::make(new BookingOrder(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])
                ->where([['status','>=',2]])
                ->orderBy('id','DESC');
            $grid->column('out_trade_no','订单编号');
            $grid->column('room_type','预定房型');
            //$grid->column('user_id','下单用户');
            $grid->column('booking_info','预定信息')->display(function(){
                $htmls =  '<span class="text-secondary">预  定 人:</span>'.$this->booking_name.'<br/>';
                $htmls .= '<span class="text-secondary">预定人电话:</span>'.$this->booking_phone.'<br/>';
                $htmls .= '<span class="text-secondary">支 付 金额:</span>'.$this->total_cost.'<br/>';
                return $htmls;
            });
            /*$grid->column('booking_name','预定人');
            $grid->column('booking_phone','预定人电话');
            $grid->column('total_cost','支付金额');*/
            ;
            $grid->column('is_confirm','接单状态')
                ->if(function ($column) {
                    return $this->pay_status == 1 && $this->status != 7;
                })
                ->using(BookingOrder::$Is_confirm)->label([
                0 => 'danger',
                1 => 'success',
                2 => Admin::color()->dark60(),
            ])->else()
                ->display(function (){
                    return '';
                })
                ->help('0取消接单 1确认接单 2等待接单');
            $grid->column('status','订单状态')->using(BookingOrder::$status_arr);
            $grid->column('remarks','预定备注');
            $grid->column('created_at','日期');
            $grid->quickSearch(['out_trade_no','booking_name','booking_phone'])->placeholder('订单编号，入住人，入住人电话');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            //$grid->export();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $row = $actions->row;
                if($row->status == 2 && $row->is_confirm == 0){
                    $modal = Modal::make('确认接单操作');
                    $modal->body(\App\Merchant\Actions\Form\ConfirmOrderForm::make()->payload($row->toArray()));
                    $modal->button("<a href='javascript:void(0);'> 确认订单</a>");
                    $actions->append($modal->render());
                    //$actions->append("<a href='javascript:void(0);' class='booking-order-confirm' data-order_no='".$row->order_no."' data-room_type='".$row->room_type."' data-booking_name='".$row->booking_name."' data-booking_phone='".$row->booking_phone."'> 确认订单</a>");
                }
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('is_confirm', '接单状态:',BookingOrder::$Is_confirm);
                //$selector->select('status', '订单状态:',BookingOrder::$status_arr);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand(false);
                $filter->equal('status','订单状态')->select(BookingOrder::$status_arr)->width(2);
                $filter->like('out_trade_no','订单编号')->width(2);
                $filter->like('booking_name','预定人')->width(2);
                $filter->like('booking_phone','预定人电话')->width(2);
            });
        });
    }
    // 预订客房订单确认 核销
    public function orderConfirm(Request $request){
        $order_no = $request->get('order_no');
        $confirm_type = $request->get('confirm_type');
        if(empty($confirm_type)){
            if($request->has('_form_')){
                return JsonResponse::make()->error('请选择确认类型');
            }
            return returnData(405,0,[],'请选择确认类型');
        }
        $where   = [];
        $where[] = ['order_no', '=', $order_no];
        $detail = BookingOrder::where($where)->first();
        if(!$detail){
            if($request->has('_form_')){
                return JsonResponse::make()->error('未找到订单信息');
            }
            return returnData(405,0,[],'未找到订单信息');
        }

        // 客房已满 预定取消 资金原路退回
        if($confirm_type == BookingOrder::confirm_type_2){
            event(new \App\Events\BookingOrderCancel($detail));
            if($request->has('_form_')){
                return JsonResponse::make()->success('订单已取消,资金原路退回')->refresh();
            }
            return returnData(200, 1, [], '订单已取消,资金原路退回');
        }

        if($detail->is_confirm == 1){
            if($request->has('_form_')){
                return JsonResponse::make()->error('订单已经确认');
            }
            return returnData(500, 0, [], '订单已经确认');
        }
        $model = BookingOrder::find($detail->id);
        $model->is_confirm = 1;
        $model->confirm_time = date('Y-m-d H:i:s');
        $model->save();

        // 给用户发送通知
        event(new \App\Events\BookingOrderConfirm($detail));
        if($request->has('_form_')){
            return JsonResponse::make()->success('操作成功')->refresh();
        }
        return returnData(200, 1, [], '操作成功');
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
        $show =  Show::make($id, new BookingOrder(), function (Show $show) {
            $show->field('id');
            $show->field('out_trade_no','订单编号');
            $show->field('room_type','预定房型');
            $show->divider();
            $show->field('booking_name','预  定 人');
            $show->field('booking_phone','预定人电话');
            $show->field('total_cost','支 付 金额');
            $show->divider();
            $show->field('is_confirm','接单状态')->using(BookingOrder::$Is_confirm);
            $show->field('status','订单状态')->using(BookingOrder::$status_arr);
            $show->field('remarks','预定备注');
            $show->field('created_at');
            $show->field('updated_at');
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return (new WidgetForm())->response()->error('没有权限');
    }
}
