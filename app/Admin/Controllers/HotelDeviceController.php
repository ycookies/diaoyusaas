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
// 列表
class HotelDeviceController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {


        // 推送交易通知内容
        /*$notice_data = [
            'appid' => 'wx7246aea8d02dabdf',
            'bank_type' => 'OTHERS',
            'cash_fee' => '300',
            'fee_type' => 'CNY',
            'is_subscribe' => 'N',
            'mch_id' => '1566291601',
            'nonce_str' => '65f92a8650dc1',
            'openid' => 'oADNc5IBIYUVZUUv-fIQWmo_6COE',
            'out_trade_no' => 'RBW2024031914024637',
            'result_code' => 'SUCCESS',
            'return_code' => 'SUCCESS',
            'sign' => '44A6E9FE8E269849F1CBB718491DB81D',
            'sub_mch_id' => '1644702947',
            'time_end' => '20240319140253',
            'total_fee' => '300',
            'trade_type' => 'JSAPI',
            'transaction_id' => '4200002147202403192836388964',
        ];

        $orderinfo = BookingOrder::with('room')->where(['out_trade_no'=>$notice_data['out_trade_no']])->first();
        // 驱动订单接单提醒
        $sn_code = HotelDevice::where(['hotel_id' => $orderinfo->hotel_id,'device_type'=> 'pos机'])->value('device_code');

        $remarks = '小程序订房:'.$orderinfo->room_type.'('.$orderinfo->days.'天 ['.$orderinfo->arrival_time.' -> '.$orderinfo->departure_time.'])';
        $remarks .= ',预订人：'.$orderinfo->booking_name.'，联系电话:'.$orderinfo->booking_phone.', 核对码:'.$orderinfo->code;
        $notice_data['hexiao_code'] = $orderinfo->code;
        $notice_data['remarks'] = $remarks;
        $notice_data['sn_code'] = $sn_code;
        $service = new RongbaoPayService();
        $res = $service->sendapi('api/payment_min_pay/wxNotifyUrlPayCode',$notice_data);
        echo "<pre>";
        print_r($notice_data);
        echo "</pre>";
        exit;*/

        return $content
            ->header('设备管理')
            ->description('全部')
            ->breadcrumb(['text'=>'设备管理','uri'=>''])
            ->body($this->grid());
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
