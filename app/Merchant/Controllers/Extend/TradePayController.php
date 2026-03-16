<?php

/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers\Extend;

use App\Models\Hotel\TradeOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Modal;
use App\Services\WxPayService;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// 当面付收款
class TradePayController extends Controller
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        $htmll = <<<HTML
<ul>
    <li>请客人出示付款码，使用手动或扫码枪快速获取</li>
</ul>
HTML;
        $alert = Alert::make($htmll, '提示:');
        $form1 = new WidgetForm();
        $form1->text('cashier_name', '收银员')->width('3')->value(Admin::user()->name)->disable();
        $form1->hidden('cashier_id', '收银员')->value(Admin::user()->id);
        $form1->number('total_fee', '收款金额')->width('4')->required();
        $form1->text('auth_code', '付款码')->help('请用户出示的付款码')->width('4')->required();
        $form1->text('order_remark', '订单备注')->width('4');
        $form1->action('extend/tradepay/pay');
        $form1->confirm('现在提交支付吗？');
        //$form1->disableResetButton();
        $card1 = Card::make('', $alert->info() . $form1);
        return $content
            ->header('当面付收款订单')
            ->description('全部')
            ->breadcrumb(['text' => '当面付收款', 'uri' => ''])
            ->row($card1)
            ->row($this->grid());
    }

    public function pay(Request $request)
    {
        $hotel_id = Admin::user()->hotel_id;
        $auth_code = $request->get('auth_code');
        $amount = $request->get('total_fee');
        $remarks  = $request->get('order_remark');
        $validator = \Validator::make($request->all(), [
            'total_fee' => 'required',
            'auth_code' => 'required',
        ], [
            'total_fee.required' => '收款金额 不能为空',
            'auth_code.required' => '付款码 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        if(WxPayService::AuthCodeType($auth_code) != 'weixin'){
            return (new WidgetForm())->response()->error('暂时只支持微信当面付，请检查付款码是否正确');
        }

        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);
        $notify_url = env('APP_URL') . '/hotel/notify/wxPayNotify/' . $config->AuthorizerAppid;

        // 组装数据
        $out_trade_no = 'TM' . time() . rand(100, 999);
        $user_id = 1;
        $insdata = [
            'hotel_id'      => $hotel_id,
            'user_id'       => $user_id,
            'out_trade_no'  => $out_trade_no,
            'type'          => TradeOrder::Type_102, //
            'fina_type'     => 'in',
            'pay_ways'      => 'wxpay',
            'total_amount' => $amount,
            'real_amount'   => $amount,
            'pay_status'    => 0,
            'remarks'       => $remarks,
        ];
        TradeOrder::addOrder($insdata);

        $undata = [
            'body' => '酒店客房日用品消费',
            'out_trade_no' => $out_trade_no,
            'total_fee' => bcmul($amount, 100, 0),
            'auth_code' => $auth_code,
        ];

        $result = $app->pay($undata);
        addlogs('TradePay', $undata, $result);
        info($result);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['result_code']) && $result['result_code'] == 'SUCCESS') {

            TradeOrder::upOrder($out_trade_no, $result);
            return (new WidgetForm())->response()->success('支付成功');
        }

        if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            $return_msg = !empty($result['return_msg']) ? $result['return_msg'] : '支付失败';
            return (new WidgetForm())->response()->error($return_msg);
        }

        //用户正在输入密码
        if (!empty($result['err_code']) && $result['err_code'] == "USERPAYING") {
            //暂停10秒
            $x = 1;
            do {
                sleep(2);
                $status = $this->WxOrderStatus($out_trade_no);
                $x++;
            } while (!empty($status['trade_state']) && $status['trade_state'] == "USERPAYING" && $x <= 25);

            // 订单支付成功
            if (!empty($status['trade_state']) && $status['trade_state'] == 'SUCCESS') {
                addlogs('TradePay-USERPAYING', $undata, $status);
                TradeOrder::upOrder($out_trade_no, $status);
                return (new WidgetForm())->response()->success('支付成功');
            }

            TradeOrder::upOrder($out_trade_no, $status);
            $return_msg = !empty($status['return_msg']) ? $status['return_msg'] : '支付失败';
            return (new WidgetForm())->response()->error($return_msg);
        }

        TradeOrder::upOrder($out_trade_no, $result);
        $return_msg = !empty($result['return_msg']) ? $result['return_msg'] : '支付失败';
        return (new WidgetForm())->response()->error($return_msg);
    }


    // 查询支付订单
    public function WxOrderStatus($out_trade_no)
    {
        $hotel_id = Admin::user()->hotel_id;
        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);

        return $app->order->queryByOutTradeNumber($out_trade_no);
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(TradeOrder::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'type'=>102])->orderBy('id', 'DESC');
            $grid->column('id');
            //$grid->column('user.nick_name', '用户');
            $grid->column('out_trade_no','订单号');
            /*$grid->column('trade_no');
            $grid->column('type');
            $grid->column('fina_type');
            $grid->column('PayWays');*/
            $grid->column('total_amount','收款金额');
            /*$grid->column('real_amount');
            $grid->column('rate');
            $grid->column('rate_amount');
            $grid->column('hb_fq_num');
            $grid->column('hb_fq_seller_percent');
            $grid->column('hb_fq_sxf');
            $grid->column('buyer_id');
            $grid->column('status');
            $grid->column('remark');

            $grid->column('cost_rate');
            $grid->column('service_rate');*/
            $grid->column('pay_status','支付状态')->using(TradeOrder::$pay_status_arr)->label(TradeOrder::$pay_status_label);
            $grid->column('created_at');
            $grid->quickSearch(['out_trade_no'])->placeholder('订单号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->disableFilterButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('out_trade_no', '订单号');
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
        return Show::make($id, new TradeOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('user_id');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('type');
            $show->field('fina_type');
            $show->field('PayWays');
            $show->field('total_amount');
            $show->field('real_amount');
            $show->field('rate');
            $show->field('rate_amount');
            $show->field('hb_fq_num');
            $show->field('hb_fq_seller_percent');
            $show->field('hb_fq_sxf');
            $show->field('buyer_id');
            $show->field('status');
            $show->field('remark');
            $show->field('pay_status');
            $show->field('cost_rate');
            $show->field('service_rate');
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
        return Form::make(new TradeOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('user_id');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('type');
            $form->text('fina_type');
            $form->text('PayWays');
            $form->text('total_amount');
            $form->text('real_amount');
            $form->text('rate');
            $form->text('rate_amount');
            $form->text('hb_fq_num');
            $form->text('hb_fq_seller_percent');
            $form->text('hb_fq_sxf');
            $form->text('buyer_id');
            $form->text('status');
            $form->text('remark');
            $form->text('pay_status');
            $form->text('cost_rate');
            $form->text('service_rate');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
