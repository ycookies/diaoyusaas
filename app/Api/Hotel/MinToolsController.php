<?php

namespace App\Api\Hotel;

use App\Admin;
use Illuminate\Http\Request;

/**
 * 小工具
 */
class MinToolsController extends BaseController
{

    // 根据内容生成二维码
    public function getQrcode(Request $request)
    {
        $qrcode_con = $request->get('qrcode_con','');
        if(empty($qrcode_con)){
            return returnData(204, 0, [], '二维码内容不能为空');
        }
        $qrCode = \QrCode::format('png')->size(200)->generate($qrcode_con);
        header('Content-Type: image/png');
        echo $qrCode;
        /*$base64 = base64_encode(\QrCode::format('png')->size(100)->generate($qrcode_con));
        echo "data:image/png;base64,".$base64;*/
        exit;
    }
}
