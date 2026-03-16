<?php

namespace Dcat\Admin\Wxpay\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Routing\Controller;

class WxpayController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('微信支付')
            ->description('微信公众号支付')
            ->body(Admin::view('eric.wxpay::index'));
    }
}