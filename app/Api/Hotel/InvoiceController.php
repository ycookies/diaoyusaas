<?php

namespace App\Api\Hotel;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\Invoicerecord;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 数电发票
class InvoiceController extends BaseController {


    // 我的发票列表
    public function getLists(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Error $error) {
            return returnData(403, 0, [], '未登陆');
        } catch (\Exception $exception) {
            return returnData(403, 0, [], '未登陆');
        }
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $where    = [
            'user_id'  => $user->id,
            'hotel_id' => $hotel_id,
        ];
        $list     = Invoicerecord::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 订单开票详情
    public function getDetail(Request $request) {
        $user         = JWTAuth::parseToken()->authenticate();
        $hotel_id     = $request->get('hotel_id');
        $out_trade_no = $request->get('out_trade_no');
        $fuwu_type    = $request->get('fuwu_type');
        $request->validate(
            [
                'hotel_id'     => 'required',
                'out_trade_no' => 'required',
                'fuwu_type'    => 'required',
            ], [
                'hotel_id.required'     => '酒店ID 不能为空',
                'out_trade_no.required' => '订单编号 不能为空',
                'fuwu_type.required'    => '消费类型 不能为空',
            ]
        );
        $info = Invoicerecord::where(['fuwu_type' => $fuwu_type, 'goods_order_no' => $out_trade_no])->first();
        return returnData(200, 1, ['info' => $info], 'ok');
    }

    // 开具数电发票
    public function invoicing(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Error $error) {
            return returnData(403, 0, [], '未登陆');
        } catch (\Exception $exception) {
            return returnData(403, 0, [], '未登陆');
        }

        $request->validate(
            [
                'hotel_id'      => 'required',
                'out_trade_no'  => 'required',
                'invoice_title' => 'required',
                'user_email'    => 'required|email',
                'fuwu_type'     => 'required',

            ], [
                'hotel_id.required'      => '酒店ID 不能为空',
                'out_trade_no.required'  => '订单编号 不能为空',
                'invoice_title.required' => '发票抬头 不能为空',
                'user_email.required'    => '接受开票通知邮箱 不能为空',
                'user_email.email'       => '接受开票通知邮箱 格式不正确',
                'fuwu_type.required'     => '消费开票类型 不能为空',
            ]
        );
        $hotel_id      = $request->get('hotel_id');
        $out_trade_no  = $request->get('out_trade_no');
        $invoice_title = $request->get('invoice_title');
        $email         = $request->get('user_email');
        $fuwu_type     = $request->get('fuwu_type');

        $order                         = BookingOrder::where(['out_trade_no' => $out_trade_no])->first();
        if(!empty($order->total_cost)){
            $total_cost = $order->total_cost;
        }else{
            $total_cost = $request->get('total_cost');
        }
        $orderNo                       = date('YmdHis') . rand(1000, 9999);
        $insert_data                   = [];
        $insert_data['orderNo']        = $orderNo;
        $insert_data['goods_order_no'] = $out_trade_no;
        $insert_data['hotel_id']       = $hotel_id;
        $insert_data['fuwu_type']      = $fuwu_type; // 消费开票类型
        $insert_data['is_check']       = 2; // 不需要审核开票
        $insert_data['is_from']        = Invoicerecord::Is_from_4;
        $insert_data['check_status']   = 2;
        // 微信发票抬头 示例
        /*array(
            'type'           => '0',
            'title'          => '广州腾讯科技有限公司',
            'taxNumber'      => '91440101327598294H',
            'telephone'      => '020-81167888',
            'bankAccount'    => '1209 0928 2210 301',
            'bankName'       => '招商银行股份有限公司广州市体育东路支行',
            'companyAddress' => '广州市海珠区新港中路397号自编72号(商业街F5-1)',
        );*/
        $insert_data['kaipiao_type']     = $invoice_title['type'];
        $insert_data['buyerName']        = !empty($invoice_title['title']) ? $invoice_title['title'] : ''; //购买人 即发票抬头
        $insert_data['buyerPhone']       = !empty($invoice_title['telephone']) ? $invoice_title['telephone'] : '';//购买人手机
        $insert_data['buyerTaxNum']      = !empty($invoice_title['taxNumber']) ? $invoice_title['taxNumber'] : '';//购买人公司纳税人识别号
        $insert_data['buyerBankName']    = !empty($invoice_title['bankName']) ? $invoice_title['bankName'] : '';//购买人公司银行
        $insert_data['buyerBankAccount'] = !empty($invoice_title['bankAccount']) ? $invoice_title['bankAccount'] : '';//购买人公司银行
        $insert_data['buyerAddress']     = !empty($invoice_title['companyAddress']) ? $invoice_title['companyAddress'] : '';
        $insert_data['goodsAmount']      = $total_cost;
        $insert_data['goodsInfo']        = '酒店住宿费';
        $insert_data['takerTel']         = $user->phone;//收票人手机 默认为公司抬头中的联系方式
        $insert_data['takerEmail']       = $email;
        $insert_data['drawerName']       = '杨光';//开票人
        //$insert_data['mer_id']           = '0';
        $insert_data['user_id']        = $user->id;
        $insert_data['invoice_status'] = 'wait';
        $info                          = Invoicerecord::firstOrCreate(['orderNo' => $orderNo], $insert_data);

        // 提交开具发票
        $service = new InvoiceService();
        $service->bookingRoom($hotel_id,$total_cost,$info);
        return returnData(200, 1, [], 'ok');

    }


}
