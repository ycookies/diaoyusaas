<?php

namespace App\Merchant\Controllers\Wxgzh;

use App\Http\Controllers\Controller;
use App\Merchant\Actions\Form\ViewTiyanQrcodeForm;
use App\Merchant\Actions\Form\WxMinAppFabuForm;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\WxappConfig;
use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;

// 微信公众号 菜单
class GzhMenuController extends Controller {

    public $oauth;
    public function getMenuList(Content $content) {
        $grid = '';
        $wxOpen = app('wechat.open');
        $res = $wxOpen->hotelWxgzh(Admin::user()->hotel_id)->menu->current();
        info('酒店ID的菜单：'.Admin::user()->hotel_id);
        info($res);
        $selfmenu_info = [];
        if(!empty($res['selfmenu_info'])){
            $selfmenu_info = $res['selfmenu_info'];
        }
        return returnData(200,1,$selfmenu_info,'ok');
    }

    /**
     * @desc 更新菜单
     * @param Request $request
     * author eRic
     * dateTime 2025-06-06 08:07
     */
    public function updateMenu(Request $request){
        $menu_json = $request->get('menu_json');
        $menu_json = json_decode($menu_json,true);

        $miniprogram_appid        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'minapp'])->value('AuthorizerAppid');

        if(empty($miniprogram_appid)){
            return returnData(200,0,[],'公众号未绑定小程序');
        }

        if(empty($menu_json['menu']['button'])){
            return returnData(200,0,[],'更新的菜单内容无法获取');
        }
        return returnData(200,1,[],'保存成功');
        /*foreach ($menu_json['menu']['button'] as $key => $item){
            if(empty($item['key'])){
                return returnData(200,0,[],$item['name'].'-菜单的值，必须填写');
            }
        }*/

        /*$menuData = [
            'is_menu_open' => 1,
            'selfmenu_info' => [
                'button' => [
                    [
                        'name' => '卡类激活',
                        'sub_button' => [
                            [
                                'type' => 'miniprogram',
                                'name' => '季卡激活',
                                'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                                'appid' => 'wx6d5f2280f6658420',
                                'pagepath' => '/pages/season_card/index?zkt_channel_menu=wx_menu'
                            ],
                            [
                                'type' => 'miniprogram',
                                'name' => '畅住年卡激活',
                                'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                                'appid' => 'wx6d5f2280f6658420',
                                'pagepath' => '/pages/year_card/index?zkt_channel_menu=wx_menu'
                            ],
                            [
                                'type' => 'miniprogram',
                                'name' => '个人中心',
                                'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                                'appid' => 'wx6d5f2280f6658420',
                                'pagepath' => '/pages/user/index?zkt_channel_menu=wx_menu'
                            ]
                        ]
                    ],
                    [
                        'name' => '199季卡',
                        'sub_button' => [
                            [
                                'type' => 'view',
                                'name' => '199季卡抢购',
                                'url' => 'https://zd.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=%2Fr_ticket_poster%3Fid%3D3864561%26source%3Dofficial_menu%26appid%3Dwx2d4950026ab50edb%26user_id%3D1176023%26product_grouptype%3D0%26tj_bm_user_id%3D1176023%26hid%3D183327%26fs%3D252658895%26zkt_scene%3Dsale%26zkt_sign%3D1-bbea51%26zkt_channel_menu%3Dwx_menu'
                            ],
                            [
                                'type' => 'view',
                                'name' => '官网商城',
                                'url' => 'https://zd1.zhiketong.com/Room/Hotel/appid_wx2d4950026ab50edb?hotel_id=183327&hid=183327&appid=wx2d4950026ab50edb&fs=17083210&zkt_scene=sale&zkt_sign=1-bbea51a9848ebabec81f6e979c732585&zkt_channel_menu=wx_menu'
                            ],
                            [
                                'type' => 'view',
                                'name' => '618钜惠',
                                'url' => 'https://zd.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=%2FCustomizesnapshot%2Fappid_wx2d4950026ab50edb%3Fid%3D274207%26appid%3Dwx2d4950026ab50edb%26fs%3D253673073%26source%3Dofficial_artical%26tj_bm_user_id%3D2490615%26hid%3D183327%26zkt_scene%3Dsale%26zkt_sign%3D1-bbea51%26zkt_channel_menu%3Dwx_menu'
                            ]
                        ]
                    ],
                    [
                        'name' => '会员权益',
                        'sub_button' => [
                            [
                                'type' => 'view',
                                'name' => '会员中心',
                                'url' => 'http://zd.zhiketong.com/r_member?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                            ],
                            [
                                'type' => 'view',
                                'name' => '我的积分',
                                'url' => 'https://zd.zhiketong.com/Point/Mall?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                            ],
                            [
                                'type' => 'view',
                                'name' => 'VR全景图',
                                'url' => 'http://720yun.com/t/bddjzsefrv2?pano_id=6288609&zkt_channel_menu=wx_menu&action=wx'
                            ],
                            [
                                'type' => 'view',
                                'name' => '会员充值',
                                'url' => 'https://zd1.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=https%3A%2F%2Fzd.zhiketong.com%2FPrepayCard%2FcDeposit%3Fhotel_id%3D183327%26appid%3Dwx2d4950026ab50edb%26prepayCardType%3D1%26fs%3D5439982%26zkt_channel_menu%3Dwx_menu'
                            ]
                        ]
                    ]
                ]
            ]
        ];*/

        $buttons = $menu_json['menu']['button'];
        foreach ($buttons as $key => &$item){
            if(isset($item['type']) && $item['type'] == 'miniprogram'){
                $item['appid'] = $miniprogram_appid;
                $item['pagepath'] = preg_replace('@^/+@', '', $item['key']);
                unset($item['key']);
            }
            if(isset($item['sub_button'])){
                foreach ($buttons as $key => &$sub_item){
                    if(isset($sub_item['type']) && $sub_item['type'] == 'miniprogram'){
                        $sub_item['appid'] = $miniprogram_appid;
                        $sub_item['pagepath'] = preg_replace('@^/+@', '', $item['key']);
                        unset($sub_item['key']);
                    }
                }
            }
        }
        if($miniprogram_appid == 'wx8c3269cc7aad2bd7'){
            /*$buttons =  [
                [
                    'name' => '卡类激活',
                    'sub_button' => [
                        [
                            'type' => 'miniprogram',
                            'name' => '季卡激活',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/season_card/index?zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '畅住年卡激活',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/year_card/index?zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '个人中心',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/user/index?zkt_channel_menu=wx_menu'
                        ]
                    ]
                ],
                [
                    'name' => '订房',
                    'type' => 'miniprogram',
                    'appid' => 'wx8c3269cc7aad2bd7',
                    'pagepath' => 'pages/index/index'
                ],
                [
                    'name' => '会员权益',
                    'sub_button' => [
                        [
                            'type' => 'miniprogram',
                            'name' => '会员中心',
                            'appid' => 'wx8c3269cc7aad2bd7',
                            'pagepath' => 'pages/my/my'
                            //'url' => 'http://zd.zhiketong.com/r_member?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '我的积分',
                            'appid' => 'wx8c3269cc7aad2bd7',
                            'pagepath' => 'pages2/extend/jifen'
                            //'url' => 'https://zd.zhiketong.com/Point/Mall?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'view',
                            'name' => 'VR全景图',
                            'url' => 'http://720yun.com/t/bddjzsefrv2?pano_id=6288609&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '会员充值',
                            'appid' => 'wx8c3269cc7aad2bd7',
                            'pagepath' => 'pages2/extend/chuzhi'
                            //'url' => 'https://zd1.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=https%3A%2F%2Fzd.zhiketong.com%2FPrepayCard%2FcDeposit%3Fhotel_id%3D183327%26appid%3Dwx2d4950026ab50edb%26prepayCardType%3D1%26fs%3D5439982%26zkt_channel_menu%3Dwx_menu'
                        ]
                    ]
                ]
            ];*/
            $buttons = [
                [
                    'name' => '卡类激活',
                    'sub_button' => [
                        [
                            'type' => 'miniprogram',
                            'name' => '季卡激活',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/season_card/index?zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '畅住年卡激活',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/year_card/index?zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'miniprogram',
                            'name' => '个人中心',
                            'url' => 'https://acard.zilongwan.com.cn/?zkt_channel_menu=wx_menu',
                            'appid' => 'wx6d5f2280f6658420',
                            'pagepath' => '/pages/user/index?zkt_channel_menu=wx_menu'
                        ]
                    ]
                ],
                [
                    'name' => '199季卡',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '199季卡抢购',
                            'url' => 'https://zd.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=%2Fr_ticket_poster%3Fid%3D3864561%26source%3Dofficial_menu%26appid%3Dwx2d4950026ab50edb%26user_id%3D1176023%26product_grouptype%3D0%26tj_bm_user_id%3D1176023%26hid%3D183327%26fs%3D252658895%26zkt_scene%3Dsale%26zkt_sign%3D1-bbea51%26zkt_channel_menu%3Dwx_menu'
                        ],
                        [
                            'type' => 'view',
                            'name' => '官网商城',
                            'url' => 'https://zd1.zhiketong.com/Room/Hotel/appid_wx2d4950026ab50edb?hotel_id=183327&hid=183327&appid=wx2d4950026ab50edb&fs=17083210&zkt_scene=sale&zkt_sign=1-bbea51a9848ebabec81f6e979c732585&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'view',
                            'name' => '618钜惠',
                            'url' => 'https://zd.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=%2FCustomizesnapshot%2Fappid_wx2d4950026ab50edb%3Fid%3D274207%26appid%3Dwx2d4950026ab50edb%26fs%3D253673073%26source%3Dofficial_artical%26tj_bm_user_id%3D2490615%26hid%3D183327%26zkt_scene%3Dsale%26zkt_sign%3D1-bbea51%26zkt_channel_menu%3Dwx_menu'
                        ]
                    ]
                ],
                [
                    'name' => '会员权益',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '会员中心',
                            'url' => 'http://zd.zhiketong.com/r_member?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'view',
                            'name' => '我的积分',
                            'url' => 'https://zd.zhiketong.com/Point/Mall?appid=wx2d4950026ab50edb&zkt_channel_menu=wx_menu'
                        ],
                        [
                            'type' => 'view',
                            'name' => 'VR全景图',
                            'url' => 'http://720yun.com/t/bddjzsefrv2?pano_id=6288609&zkt_channel_menu=wx_menu&action=wx'
                        ],
                        [
                            'type' => 'view',
                            'name' => '会员充值',
                            'url' => 'https://zd1.zhiketong.com/wx2d4950026ab50edb/MidJump/r?show=0&page=0&url=https%3A%2F%2Fzd.zhiketong.com%2FPrepayCard%2FcDeposit%3Fhotel_id%3D183327%26appid%3Dwx2d4950026ab50edb%26prepayCardType%3D1%26fs%3D5439982%26zkt_channel_menu%3Dwx_menu'
                        ]
                    ]
                ]
            ];
        }



        $wxOpen = app('wechat.open');
        $menuobj = $wxOpen->hotelWxgzh(Admin::user()->hotel_id)->menu->create($buttons);

        if($menuobj['errcode'] == 0 && $menuobj['errmsg'] == 'ok'){
            return returnData(200,1,[],'保存成功，已发布了公众号菜单设置');
        }
        $errmsg = !empty($menuobj['errmsg']) ? $menuobj['errmsg']:'未知错误';
        return returnData(405,0,[],$errmsg);

    }
}
