<?php

namespace App\Merchant\Controllers\Wxgzh;

use App\Http\Controllers\Controller;
use App\Models\Hotel\WxCardTpl;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Illuminate\Http\Request;

// 微信公众号 卡券
class CardController extends Controller {

    public $oauth;

    public function index(Content $content) {
        $seller = Admin::user();
        $wxOpen = app('wechat.open');
        $gzhobj = $wxOpen->hotelWxgzh($seller->hotel_id);

        //$result = $gzhobj->material->uploadImage(public_path('img/card-bg2.png'));
        //$result = $gzhobj->material->uploadImage(public_path('img/card-logo.png'));
        $result = $this->addCard();
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;

        return $content
            ->header('公众号 卡券管理')
            ->description('卡券管理')
            ->breadcrumb(['text' => '公众号 卡券管理', 'uri' => ''])
            ->body('');
    }

    // 添加测试白名单 open_id
    public function testwhitelist(Request $request) {
        $seller  = Admin::user();
        $wxOpen  = app('wechat.open');
        $gzhobj  = $wxOpen->hotelWxgzh($seller->hotel_id);
        $openids = $request->get('openids'); // 多个open_id
        if (!is_array($openids)) {
            $openids = explode(',', $openids);
        }
        $cardInfo = $gzhobj->card->setTestWhitelist($openids); // 使用 openid
        //$cardInfo = $gzhobj->card->setTestWhitelistByName($usernames); // 使用 username
        return returnData(200, 1, ['res' => $cardInfo], 'ok');
    }

    public function createQrCode(Request $request) {
        $card_id  = $request->get('card_id');
        $seller   = Admin::user();
        $wxOpen   = app('wechat.open');
        $hotel_id = $request->get('hotel_id');
        $gzhobj   = $wxOpen->hotelWxgzh($hotel_id);
        $cards    = [
            'action_name'    => 'QR_CARD',
            'expire_seconds' => 1800,
            'action_info'    => [
                'card' => [
                    'card_id'        => $card_id,
                    //'code' => '2345679800001',
                    'is_unique_code' => true,
                    'outer_id'       => 1,
                ],
            ],
        ];
        $cardInfo = $gzhobj->card->createQrCode($cards);
        if (!empty($cardInfo['show_qrcode_url'])) {
            return JsonResponse::make()->data($cardInfo)->success('获取成功')->refresh();
        }
        $error_msg = !empty($resl['errmsg']) ? $resl['errmsg'] : '-';
        return JsonResponse::make()->data($cardInfo)->error('删除失败:' . $error_msg);
        /**
         * "errcode": 0,
         * "errmsg": "ok",
         * "ticket": "gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA",
         * "expire_seconds": 1800,
         * "url": "http://weixin.qq.com/q/02y6zwBe6mdoG1f0qFhC4O",
         * "show_qrcode_url": "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA"
         * }
         */
    }

    public function getDetail(Request $request) {
        $card_id  = $request->get('card_id');
        $seller   = Admin::user();
        $wxOpen   = app('wechat.open');
        $gzhobj   = $wxOpen->hotelWxgzh($seller->hotel_id);
        $cardInfo = $gzhobj->card->get('pRpZY6i9IEmwH9PmLH-MkaYqAs8s');

        return returnData(200, 1, ['res' => $cardInfo], 'ok');
    }

    // 删除卡券
    public function delCard(Request $request) {
        $cardId = $request->get('card_id');
        $seller = Admin::user();
        $wxOpen = app('wechat.open');
        $gzhobj = $wxOpen->hotelWxgzh($seller->hotel_id);
        $res    = $gzhobj->card->delete($cardId);
        if (isset($res['errcode']) && $res['errcode'] == 0) {
            $updata = [
                'card_id' => '',
                'status'  => 1,
            ];
            WxCardTpl::where(['card_id' => $cardId])->update($updata);
            return JsonResponse::make()->data($res)->success('删除成功')->refresh();
        }
        addlogs('delCard', ['card_id' => $cardId], $res, $seller->hotel_id);
        $error_msg = !empty($resl['errmsg']) ? $resl['errmsg'] : '-';
        return JsonResponse::make()->data($res)->error('删除失败:' . $error_msg);
    }

    // 创建会员卡模板
    public function addCard(Request $request) {
        $seller      = Admin::user();
        $card_tpl_id = $request->get('card_tpl_id');
        if (empty($card_tpl_id)) {
            return JsonResponse::make()->error('会员卡模板ID 不能为空');
        }
        $tpl_info = WxCardTpl::where(['id' => $card_tpl_id])->first();
        if ($tpl_info->card_id != '') {
            return JsonResponse::make()->error('会员卡模板信息未找到');
        }
        $wxOpen     = app('wechat.open');
        $oauth_info = $wxOpen->getOauthInfo('', $seller->hotel_id);

        if(!$oauth_info){
            return JsonResponse::make()->error('公众号还没有授权给平台,请前去授权');
        }
        $gzhobj     = $wxOpen->hotelWxgzh($seller->hotel_id);

        $bgimg_path = str_replace(env('APP_URL') . '/', '', $tpl_info->background_pic_url);

        $logo_path = str_replace(env('APP_URL') . '/', '', $tpl_info->logo_url);

        /**
         * Array
         * (
         * [media_id] => bhrT2juvfCkhbov1_DkpzaiwNDihXa2qUxGqcITEQB6aWL3bPek_zOANcXH1iEvc
         * [url] => http://mmbiz.qpic.cn/sz_mmbiz_png/Bq2nys5Z4bbCDMiaG3qk70vRSYdd8VdICaNx6rJkAjqczYdNDkjnNCMHVkgRub2aEvcPnST9O0ShOBsTM5KxiaXA/0?wx_fmt=png
         * [item] => Array
         * (
         * )
         *
         * )
         */
        $background_pic_url = '';
        $logo_url           = '';
        $result1            = $gzhobj->material->uploadImage(public_path($bgimg_path));//上传素材
        if (empty($result1['url'])) {
            addlogs('material_uploadImage', ['card_tpl_id' => $card_tpl_id, 'bgimg_path' => $bgimg_path], $result1, $seller->hotel_id);
            return JsonResponse::make()->error('会员卡模板 背景图片素材上传失败');
        }
        if (!empty($result1['url'])) {
            $background_pic_url = $result1['url'];
        }

        $result2 = $gzhobj->material->uploadImage(public_path($logo_path));//上传素材
        if (empty($result2['url'])) {
            addlogs('material_uploadImage', ['card_tpl_id' => $card_tpl_id, 'logo_path' => $logo_path], $result2, $seller->hotel_id);
            return JsonResponse::make()->error('会员卡模板 背景图片素材上传失败');
        }
        if (!empty($result2['url'])) {
            $logo_url = $result2['url'];
        }

        if (!empty($background_pic_url) && !empty($logo_url)) {
        } else {
            return JsonResponse::make()->error('会员卡模板 背景图片,商户logo 素材上传失败');
        }

        $attributes = [
            'background_pic_url'           => $background_pic_url, // 商家自定义会员卡背景图 1000*600
            'base_info'                    => [
                "logo_url"                      => $logo_url, //卡券的商户logo，建议像素为300*300。
                'brand_name'                    => $tpl_info->brand_name, // 商户名字,字数上限为12个汉字
                'code_type'                     => 'CODE_TYPE_QRCODE', // Code展示类型 "CODE_TYPE_TEXT" 文本 "CODE_TYPE_BARCODE" 一维码 "CODE_TYPE_QRCODE" 二维码 "CODE_TYPE_ONLY_QRCODE" 仅显示二维码 "CODE_TYPE_ONLY_BARCODE" 仅显示一维码 "CODE_TYPE_NONE" 不显示任何码型
                'title'                         => $tpl_info->title, // 卡券名，字数上限为9个汉字
                "color"                         => $tpl_info->colors, // 券颜色
                "notice"                        => $tpl_info->notice, //卡券使用提醒，字数上限为16个汉字。
                "service_phone"                 => $tpl_info->service_phone, // 客服电话
                "description"                   => $tpl_info->description, // 卡券使用说明，字数上限为1024个汉字。
                "date_info"                     => [
                    "type" => "DATE_TYPE_PERMANENT", // 永久有效
                    //"type" => "DATE_TYPE_FIX_TIME_RANGE", //有效日期
                    //"type" => "DATE_TYPE_FIX_TERM" // 有效天数
                ],
                "sku"                           => [
                    "quantity" => 0 // 库存 卡券库存的数量，上限为100000000。
                ],
                "use_custom_code"               => true, // 是否自定义Code码。填写true或false，默认为false 通常自有优惠码系统的开发者选择自定义Code码，详情见 是否自定义code
                "get_custom_code_mode"          => 'GET_CUSTOM_CODE_MODE_DEPOSIT', // 填入 GET_CUSTOM_CODE_MODE_DEPOSIT 表示该卡券为预存code模式卡券， 须导入超过库存数目的自定义code后方可投放， 填入该字段后，quantity字段须为0,须导入code 后再增加库存
                "get_limit"                     => 1, // 每人可领券的数量限制，建议会员卡每人限领一张

                "can_give_friend"               => false, //卡券是否可转赠,默认为true
                "custom_url_name"               => "快速订房", // 自定义跳转外链的入口名字
                "custom_url"                    => env('APP_URL'), // 自定义跳转的URL
                "custom_app_brand_user_name"    => $oauth_info->ToUserName . "@app",
                "custom_app_brand_pass"         => "/pages/index/index",
                "custom_url_sub_title"          => "优惠快捷", // 显示在入口右侧的提示语。
                "promotion_url_name"            => "开发票", // 营销场景的自定义入口名称
                "promotion_url"                 => env('APP_URL'), // 入口跳转外链的地址链接
                "promotion_app_brand_user_name" => $oauth_info->ToUserName . "@app",
                "promotion_app_brand_pass"      => "/pages2/extend/user_invoice",
                //"promotion_ url_sub_title" => '右侧的提示语',// 显示在营销入口右侧的提示语

                //"center_title"               => '快速订房',
                //"center_sub_title"           => '立享超级优惠',
                //"center_url"                 => env('APP_URL'),
                //"center_app_brand_user_name" => $oauth_info->ToUserName . "@app",
                //"center_app_brand_pass"      => '/pages/index/index',
                "pay_info" => [
                    'swipe_card'=> [
                        'is_swipe_card' => true,
                    ]
                ],
                "need_push_on_view" => true // 填写true为用户点击进入会员卡时推送事件，默认为false。详情见 进入会员卡事件推送
            ],
            "supply_bonus"                 => true, //显示积分，填写true或false，如填写true，积分相关字段均为必 填 若设置为true则后续不可以被关闭。
            "bonus_url"                    => env('APP_URL'),
            "bonus_app_brand_user_name"    => $oauth_info->ToUserName . "@app",
            "bonus_app_brand_pass"         => "/pages2/extend/jifen",
            "supply_balance"               => false, // 是否支持储值，填写true或false。如填写true，储值相关字段均为必 填 若设置为true则后续不可以被关闭。该字段须开通储值功能后方可使用， 详情见： 获取特殊权限
            /*"balance_url"                  => env('APP_URL'),
            "balance_app_brand_user_name"  => $oauth_info->ToUserName . "@app",
            "balance_app_brand_pass"       => "/pages2/extend/chuzhi",*/
            "prerogative"                  => $tpl_info->prerogative, // 会员卡特权说明,限制1024汉字
            "auto_activate"                => false, // 设置为true时用户领取会员卡后系统自动将其激活，无需调用激活接口，详情见 自动激活
            "custom_field1"                => [ // 自定义会员信息类目，会员卡激活后显示,包含name_type (name) 和url字段
                                                "name_type"           => "FIELD_NAME_TYPE_LEVEL", // 会员信息类目半自定义名称
                                                "name"                => "等级", // 会员信息类目自定义名称
                                                "url"                 => env('APP_URL'), // 点击类目跳转外链url
                                                "app_brand_user_name" => $oauth_info->ToUserName . "@app",
                                                "app_brand_pass"      => "/pages2/extend/user_level",
            ],
            //"activate_url"                 => env('APP_URL'), // 激活会员卡的url
            "activate_app_brand_user_name" => $oauth_info->ToUserName . "@app",
            "activate_app_brand_pass"      => "/pages/index/index",
            "wx_activate" => true,
            "wx_activate_after_submit" => true,
            "wx_activate_after_submit_url" => "pages/index/index",
            "custom_cell1"                 => [ // 自定义会员信息类目，会员卡激活后显示。
                                                "name"                => "停车缴费", // 入口名称。
                                                "tips"                => "无须等待", // 入口右侧提示语，6个汉字内。
                                                "url"                 => env('APP_URL'), // 入口跳转链接。
                                                "app_brand_user_name" => $oauth_info->ToUserName . "@app",
                                                "app_brand_pass"      => "/pages2/extend/parking_pay_car_cost",
            ],
            "custom_cell2"                 => [ // 自定义会员信息类目，会员卡激活后显示。
                                                "name"                => "积分兑换", // 入口名称。
                                                "tips"                => "", // 入口右侧提示语，6个汉字内。
                                                "url"                 => env('APP_URL'), // 入口跳转链接。
                                                "app_brand_user_name" => $oauth_info->ToUserName . "@app",
                                                "app_brand_pass"      => "/pages1/pointsmall/index",
            ],
            "balance_rules"                => $tpl_info->balance_rules,// 储值说明
            //'location_id_list' => '', //门店位置ID。调用 POI门店管理接口 获取门店位置ID。
            /*"bonus_rule"                   => [ // 积分规则
                                                "cost_money_unit"          => 100,
                                                "increase_bonus"           => 1,
                                                "max_increase_bonus"       => 200,
                                                "init_increase_bonus"      => 0,
                                                "cost_bonus_unit"          => 5,
                                                "reduce_money"             => 100,
                                                "least_money_to_use_bonus" => 1000,
                                                "max_reduce_bonus"         => 50
            ],*/
            'discount'                     => '1' // 折扣

        ];
        $resl       = $gzhobj->card->create($cardType = 'member_card', $attributes);
        if (!empty($resl['card_id'])) {
            $updata = [
                'card_id' => $resl['card_id'],
                'status'  => 2,
            ];
            WxCardTpl::where(['id' => $card_tpl_id])->update($updata);

            // 添加
            return JsonResponse::make()->data($resl)->success('创建成功')->refresh();
        }

        $error_msg = !empty($resl['errmsg']) ? $resl['errmsg'] : '-';
        return JsonResponse::make()->error('创建会员卡模板失败:' . $error_msg);
    }

    public function getCardColors(Content $content) {
        $seller = Admin::user();
        $wxOpen = app('wechat.open');
        $gzhobj = $wxOpen->hotelWxgzh($seller->hotel_id);
        $mk     = $gzhobj->card->colors();
        return $mk;
    }
}
