<?php

namespace App\Api\Hotel;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\HotelSetting;

// 分享
class ShareController extends BaseController {

    public function getShareConfig(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $resl = $this->getSharePosterQrcode($request->merge(['act' => 'config']));
        if(!empty($resl['status']) && $resl['status'] != 200){
            return returnData(205, 0, [], $resl['msg']);
        }
        $poster_url = !empty($resl['data']['url']) ? $resl['data']['url']:'';
        $info = [
            'money1'            => 20,
            'money2'            => 50,
            'page_img_list'     => [
                'share1' => env('APP_URL') . '/img/share/share1.jpg',
                'share2' => env('APP_URL') . '/img/share/share22.jpg',
                'share3' => env('APP_URL') . '/img/share/share3.jpg',
            ],
            'user_share_poster' => $poster_url,
        ];

        return returnData(200, 1, ['info' => $info], 'ok');
    }

    public function getSharePosterQrcode(Request $request) {
        $user          = JWTAuth::parseToken()->authenticate();
        $hotel_id      = $request->get('hotel_id');

        $flds     = ['share_poster_bg_img'];
        $formdata = HotelSetting::getlists($flds,$hotel_id);
        $bg_img = public_path('/img/share/share4.png');
        if(!empty($formdata['share_poster_bg_img'])){
            $bg_img = public_path(str_replace(env('APP_URL'),'',$formdata['share_poster_bg_img']));;
        }
        $storage_path         = storage_path('app/public/haibao/qrcode/');
        $user_qrcode_filename = 'share-qrcode-' . $user->id . '.png';
        $full_path            = $storage_path . $user_qrcode_filename;

        /*if(file_exists($full_path)){
            unlink($full_path);
        }*/
        if (!file_exists($full_path)) {
            $wechatOpen  = app('wechat.open');
            $miniProgram = $wechatOpen->hotelMiniProgram($hotel_id);
            $page_path   = '/pages/user/share_apply?pid=' . $user->id;
            $response    = $miniProgram->app_code->get($page_path, [
                'width'      => 200,
                'line_color' => [
                    'r' => 105,
                    'g' => 166,
                    'b' => 134,
                ],
            ]);

            // 保存小程序码到文件
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $res = $response->saveAs($storage_path, $user_qrcode_filename);
            } else {
                addlogs('app_code_qrcode', [$page_path], $response);
            }
        }
        if (!file_exists($full_path)) {
            if ($request->get('act') == 'config') {
                return ['status' => 205, 'msg' => '生成小程序码失败'];
            }
            return returnData(205, 0, [], '生成小程序码失败');
        }

        $config = array(
            'text' => [],
            'image'      => array(
                array(
                    'url'     => $full_path,     //二维码资源
                    'stream'  => 0,
                    'left'    => 102,
                    'top'     => -87,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 130,
                    'height'  => 130,
                    'opacity' => 100
                ),
            ),
            'background' => $bg_img          //400X600.png背景图
        );

        $public_img_path = 'app/public/haibao/qrcode/share-poster-' . $user->id . '.png';
        $haibao_filename = storage_path($public_img_path);
        if (file_exists($haibao_filename)) {
            unlink($haibao_filename);
        }
        $ads = createPoster($config, $haibao_filename);
        if($ads === false){
            if ($request->get('act') == 'config') {
                return ['status' => 205, 'msg' => '生成推广海报失败'];
            }
            return returnData(205, 0, [], '生成推广海报失败');
        }
        $newposter_url                  = str_replace(storage_path('app/public'), env('APP_URL').'/storage', $ads);

        if ($request->get('act') == 'config') {
            return ['status' => 200,'data'=> ['url'=> $newposter_url],'msg' => 'ok'];
        }
        return returnData(200, 1, ['url'=>$newposter_url], 'ok');



        /*
        $imagePath = '';
        $shareCodeurl = '';
        $new_qrcodeurl = '';
        $res = \QrCode::format('png')
            ->errorCorrection('L')
            ->merge('path/img.png',0.5,true)
            ->backgroundColor(255, 0, 0)
            ->margin(0.5)
            ->size(190)
            ->generate($shareCodeurl, $imagePath);*/

    }

    // 获取下线用户列表
    public function getShareUserLists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $user     = JWTAuth::parseToken()->authenticate();
        $list     = User::where(['temp_parent_id' => $user->id])
            ->orWhere(['parent_id' => $user->id])
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }
}
