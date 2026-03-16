<?php

namespace App\Api\Hotel;

use App\Mail\UserInvoice;
use App\Models\Hotel\Invoicerecord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use Illuminate\Support\Facades\Mail;

// 电票异步通知

class InvoiceNotifyController extends AController {


    // 电票异步通知
    public function notify(Request $request) {
        info('电票异步通知');
        info($request->all());
        // 如果是授权回调
        if ($request->has('code') && $request->has('taxnum')) {
            return view('invoiceAuthSuccess');
        }
        $orderno = $request->get('orderno');
        if (empty($orderno)) {
            return returnData(204, 0, [], '订单编号 不能为空');
        }
        $info = Invoicerecord::where(['orderNo' => $orderno])->first();
        if (!$info) {
            return returnData(204, 0, [], '未找到开票信息');
        }

        $content_arr = json_decode($request->get('content'), true);
        if (!empty($content_arr['allElectronicInvoiceNumber'])) {
            $updata = [
                'invoiceNo'      => !empty($content_arr['allElectronicInvoiceNumber']) ? $content_arr['allElectronicInvoiceNumber'] : '',
                'invoiceCode'    => !empty($content_arr['c_fpqqlsh']) ? $content_arr['c_fpqqlsh'] : '',
                'downloadUrl'    => !empty($content_arr['c_imgUrls']) ? $content_arr['c_imgUrls'] : '',
                'invoice_status' => 'success',//开票成功
            ];
            Invoicerecord::where(['id' => $info->id])->update($updata);
            // 给用户发送邮件
            if (!empty($info->takerEmail) && $info['push_to_email'] == 0) {
                $record = Invoicerecord::where(['id' => $info->id])->first();
                $mmk = Mail::to($info->takerEmail)
                    ->send(new UserInvoice($record));
                if (empty($mmk)) {
                    $updata = [];
                    $updata['push_to_email'] = 1;
                }
            }
            Invoicerecord::where(['id' => $info->id])->update($updata);
        }

        return returnData(200, 1, [], 'ok');
    }

    // 测试电票异步通知
    public function testNotify(Request $request) {
        info('电票异步通知');
        info($request->all());
        // 如果是授权回调
        if ($request->has('code') && $request->has('taxnum')) {
            return view('invoiceAuthSuccess');
        }
        return returnData(200, 1, [], 'ok');
    }


}
