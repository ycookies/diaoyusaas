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

use App\Models\Hotel\OrderRefund;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Order\Order;
// 列表
class OrderRefundController extends AdminController
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
        return Grid::make(OrderRefund::with('user','room','hotel'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('room.name','房型');
           $grid->column('user.nick_name','退款用户')->display(function ($name){
                return $name.'<span class="f12 text-gray"> ('.$this->user_id.')</span>';
            });
            $grid->column('order_no','订单号')->display(function (){
                if($this->sign == Order::Sign_HotelRoomBooking){
                    return "<a href='".admin_url('/booking-order?_search_='.$this->order_no)."'>".$this->order_no."</a>";
                }
                return $this->order_no;
            });
            $grid->column('out_request_no');
            $grid->column('cost');
            $grid->column('refund_desc','取消预定原因');
            $grid->column('status','退款状态')
                ->using(OrderRefund::Status_arr)
                ->label([
                    'default' => 'primary',
                    0 => 'primary',
                    1 => 'info',
                    2 => 'success',
                    3 => 'danger',
                ]);
            $grid->column('created_at');
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                //
                //$actions->disableView();
            });
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

        return Show::make($id, new OrderRefund(), function (Show $show) {
            //$show->field('id');
            //$show->field('hotel_id');

            $isvpay = app('wechat.isvpay');
            $app    = $isvpay->setSubMerchant(Admin::user()->hotel_id);
            $result = $app->refund->queryByOutRefundNumber($show->model()->out_request_no);

            //$show->model()->out_request_no;
            /*echo "<pre>";print_r($show->model()->out_request_no);
            echo "</pre>";
            exit;*/
            $show->field('room_id');
            $show->field('user_id','用户ID');
            $show->field('order_no');
            $show->field('out_request_no');
            $show->field('cost');
            $show->field('refund_desc','退款描述');
            $show->field('status','退款状态')->using(OrderRefund::Status_arr);
            $show->field('created_at');
            $show->html('<pre>'.print_r($result,true).'</pre>');
            //$show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new OrderRefund(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('room_id');
            $form->text('user_id');
            $form->text('order_no');
            $form->text('out_request_no');
            $form->text('cost');
            $form->text('refund_desc');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
