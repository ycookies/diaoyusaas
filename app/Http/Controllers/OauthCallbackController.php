<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Apilog;

// 授权回调
class OauthCallbackController extends Controller
{

    // 微信开放平台授权回调
    public function wxopenCallback(Request $request){
        info($request->all());
        return 'ok';
    }

}
