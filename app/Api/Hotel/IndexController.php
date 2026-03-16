<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Ad;
use App\Models\Hotel\Assess;
use App\Models\Hotel\Banner;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\Gonggao;
use App\Models\Hotel\Help;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Huodong;
use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\Seller;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\Topic;
use App\Models\Hotel\Tuangou\TuangouGoods;
use App\Models\Hotel\UserCenterNav;
use Orion\Http\Requests\Request;

class IndexController extends BaseController {

    public $userinfo = [];

    // 获取首页配置信息
    public function index(Request $request) {
        //$this->user              = Auth::guard('api')->user();
        $home_pages['search']          = $this->search();
        $home_pages['banner']          = $this->banner();
        $home_pages['nav']             = $this->nav();
        $home_pages['notice']          = $this->notice();
        $home_pages['hotelinfo']       = $this->hotelInfo();
        $home_pages['userinfo']        = $this->userinfo();
        $home_pages['topRoom']         = $this->topRoom();
        $home_pages['topRoomSku']      = $this->topRoomSku();
        $home_pages['goods']           = $this->goods();
        $home_pages['modals']          = $this->modals();
        $home_pages['coupons']         = $this->coupons();
        $home_pages['topic']           = $this->topic();
        $home_pages['assess']          = $this->assess();
        $home_pages['huodong']         = $this->huodong();
        $home_pages['topTuangou']      = $this->tuangou();
        $home_pages['shop_config']     = $this->shopConfig();
        $home_pages['user_center_nav'] = $this->userCenterNav();

        return returnData(200, 1, ['home_pages' => $home_pages]);
    }

    // 酒店信息
    public function hotelInfo() {
        $hotelinfo = Seller::where('id', $this->hotel_id)->select('name', 'address', 'tel', 'coordinates', 'video_url', 'video_cover', 'store_wifi_password')->first();
        return [
            'hotel_name'          => $hotelinfo->name,
            'hotel_address'       => $hotelinfo->address,
            'hotel_tel'           => $hotelinfo->tel,
            'coordinates'         => $hotelinfo->coordinates,
            'video_url'           => $hotelinfo->video_url,
            'video_cover'         => $hotelinfo->video_cover,
            'store_wifi_password' => $hotelinfo->store_wifi_password,
        ];
    }

    // 用户信息
    public function userinfo() {
        $request       = Request();
        $info          = [];
        $wx_login_code = $request->get('wx_login_code', '');
        $app_id        = $request->get('app_id', '');
        $hotel_id      = $request->get('hotel_id', '');
        if (!empty($wx_login_code)) {
            $miniProgram = app('wechat.open')->miniProgram($app_id);
            $infos       = $miniProgram->auth->session($wx_login_code);
            info($infos);
            // 微信登陆失败
            if (!empty($infos['openid'])) {
                $wx_openid = $infos['openid'];
                $userinfo  = \App\User::where(['openid' => $wx_openid, 'hotel_id' => $hotel_id])->first();
                if ($userinfo) {
                    $info           = $userinfo->toArray();
                    $this->userinfo = $info;
                }
            }
        }
        return [
            'info'        => $info,
            'key'         => 'userinfo',
            'name'        => '用户资料',
            'relation_id' => 0,
        ];
    }

    // 搜索框
    public function search() {
        return [
            'key'         => 'search',
            'name'        => '搜索框',
            'relation_id' => 0,
        ];
    }

    /**
     * @desc 轮播图
     * @return array
     */
    public function banner() {
        $list = Banner::where(['hotel_id' => $this->hotel_id, 'is_active' => 1])->orderBy('sorts', 'ASC')->get();
        return [
            'list'        => $list,
            'key'         => 'banner',
            'name'        => '轮播图',
            'relation_id' => 0,
        ];
    }

    /**
     * @desc  导航图标
     * @return array
     */
    public function nav() {
        $list = HomeNav::where(['hotel_id' => $this->hotel_id, 'status' => 1])->orderBy('sort', 'ASC')->get();
        return [
            'list'        => $list,
            'key'         => 'home_nav',
            'name'        => '导航图标',
            'relation_id' => 0,
            'row_num'     => 4
        ];
    }

    /**
     * @desc  推荐房型sku
     * @return array
     */
    public function topRoomSku() {
        $request    = Request();
        $row_num    = 5; // 首页展示多少条
        $start_time = $request->get('start_time', date('Y-m-d'));
        $end_time   = $request->get('end_time', date('Y-m-d', strtotime('+1 days')));
        // ->select(['id', 'name', 'price', 'logo'])
        $list = RoomSkuPrice::with('room')->where(['hotel_id' => $this->hotel_id, 'state' => 1, 'recommend' => 1])->orderBy('id', 'DESC')->limit($row_num)->get();

        foreach ($list as $key => &$item) {
            $room_today_price = Roomprice::getRoomSkuDateRangePrice($item->id, $start_time, $end_time);

            $item->is_full_room = false;
            if (empty($item->roomsku_stock)) {
                $item->is_full_room = 0;
            }
            $item->room_type_desc   = $item->room->area . '㎡ ' . $item->room->bed_num . '张床 可住' . $item->room->people . '人';
            $item->room_today_price = !empty($room_today_price) ? formatFloat($room_today_price) : formatFloat($item->roomsku_price); // 今日房价
        }

        return [
            'list'        => $list,
            'key'         => 'top_room',
            'name'        => '推荐房型',
            'relation_id' => 0,
            'row_num'     => $row_num
        ];
    }

    /**
     * @desc  推荐房型
     * @return array
     */
    public function topRoom() {
        $request    = Request();
        $start_time = $request->get('start_time', date('Y-m-d'));
        $end_time   = $request->get('end_time', date('Y-m-d', strtotime('+1 days')));

        $list = Room::where(['hotel_id' => $this->hotel_id, 'state' => 1, 'recommend' => 1])->select(['id', 'name', 'price', 'logo'])->orderBy('sort', 'DESC')->limit(4)->get();
        foreach ($list as $key => &$item) {
            $room_today_price   = Roomprice::getRoomDateRangePrice($item->id, $start_time, $end_time);
            $item->is_full_room = false;
            if (empty($room_today_price)) {
                $item->is_full_room = true;
            }
            $item->room_today_price = !empty($room_today_price) ? formatFloats($room_today_price) : formatFloats($item->price); // 今日房价
        }

        return [
            'list'        => $list,
            'key'         => 'top_room',
            'name'        => '推荐房型',
            'relation_id' => 0,
            'row_num'     => 4
        ];
    }

    // 公告
    public function notice() {
        $gonggao = Gonggao::where(['hotel_id' => $this->hotel_id, 'is_show' => 1])->orderBy('id', 'DESC')->first();
        if (!$gonggao) {
            return [];
        }
        return [
            'is_edit'           => '1',
            'key'               => 'notice',
            'name'              => '公告',
            'notice_bg_color'   => '#ED7E78',
            'notice_content'    => $gonggao->content,
            'notice_name'       => $gonggao->title,
            'notice_text_color' => '#FFFFFF',
            'notice_url'        => '',
            'relation_id'       => 0,
        ];
    }

    // 热门商品
    public function goods() {
        $list = SmgGood::where(['hotel_id' => $this->hotel_id, 'recommend' => 1])
            ->select(['id', 'cid', 'goods_name', 'goods_img', 'price', 'recommend'])
            ->limit(4)
            ->get();
        return [
            'list'        => $list,
            'key'         => 'goods',
            'name'        => '热门商品',
            'relation_id' => 0,
            'row_num'     => 4
        ];
    }

    // 弹窗广告
    public function modals() {
        $info = Ad::where(['hotel_id' => $this->hotel_id, 'type' => '0', 'status' => 1])->orderBy('id', 'DESC')->limit(1)->get();
        return [
            'list'        => $info,
            'key'         => 'modals',
            'name'        => '弹窗广告',
            'relation_id' => 0,
        ];
    }

    // 优惠券
    public function coupons() {
        $where   = [];
        $where[] = ['hotel_id', '=', $this->hotel_id];
        $where[] = ['status', '=', 1];
        $where[] = ['type', '=', 0];
        $where[] = ['start_time', '>=', date('Y-m-d 00:00:00')];
        $list    = Coupon::where($where)->orderBy('id', 'DESC')->limit(3)->get();
        // 用户是否有领取
        foreach ($list as $key => &$items) {
            $is_receive        = 0;
            $items->is_receive = $is_receive;
        }
        return [
            'list'        => $list,
            'key'         => 'coupons',
            'name'        => '优惠券',
            'relation_id' => 0,
        ];
    }

    // 热门活动
    public function huodong() {

        $list = Huodong::where(['hotel_id' => $this->hotel_id, 'is_active' => 1, 'is_hot' => 1])
            ->select('id', 'act_code', 'act_type', 'act_name', 'act_banner', 'act_sub_banner')
            ->limit(2)->get();
        // 添加一个字段
        foreach ($list as $key => &$items) {
            $items->title = $items->act_name;
        }
        return [
            'list'        => $list,
            'key'         => 'huodong',
            'name'        => '热门活动',
            'relation_id' => 0,
        ];
    }

    // 团购
    public function tuangou() {
        $pagesize = 5;
        $hotel_id = $this->hotel_id;
        $list     = TuangouGoods::with('goods', 'warehouse')
            ->where(['hotel_id' => $hotel_id])
            ->whereHas('goods', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('sorts', 'DESC')
            ->limit($pagesize)->get();
        $module_info_list = [
            143 => [
                'module_name' => '温泉团购',
                'module_title' => '自然温泉‧极致养生',
                'module_sub_title' => '天然药泉 沸如滚汤 潇湘第一泉',
            ],
            226 => [
                'module_name' => '茶室团购',
                'module_title' => '从一杯茶开始',
                'module_sub_title' => '静坐听茶语 淡看云卷舒',
            ],
            227 => [
                'module_name' => '温泉团购',
                'module_title' => '自然温泉‧极致养生',
                'module_sub_title' => '天然药泉 沸如滚汤 潇湘第一泉',
            ],
            228 => [
                'module_name' => '中餐团购',
                'module_title' => '提前预订‧省节时间',
                'module_sub_title' => '营养健康 美味无限 商务宴请',
            ]
        ];
        $module_name = '服务团购';
        $module_title = '';
        $module_sub_title = '';

        if(!empty($module_info_list[$hotel_id])){
            $module_info_one = $module_info_list[$hotel_id];
            $module_name = $module_info_one['module_name'];
            $module_title = $module_info_one['module_title'];
            $module_sub_title = $module_info_one['module_sub_title'];
        }
        return [
            'list'             => $list,
            'key'              => 'tuangou',
            'name'             => '服务团购',
            'module_name'      => $module_name,
            'module_title'     => $module_title,
            'module_sub_title' => $module_sub_title,
            'relation_id'      => 0,
        ];
    }

    public function shopConfig() {
        $card_info   = \App\Models\Hotel\WxCardTpl::where(['hotel_id' => $this->hotel_id])->first();
        $card_status = 0;
        if ($card_info->status == 2) {
            $card_status = 1;
        }
        $field_arr = [
            'user_regiser_required_wxcard',
            'user_update_info_required',
        ];
        $config    = HotelSetting::getlists($field_arr, $this->hotel_id);
        $setting   = array_merge([
            'wx_member_card'      => $card_status, //微信会员卡是否开通
            'wx_member_card_info' => $card_info ? $card_info->toArray() : [],
        ], $config);
        return $setting;
    }

    // 热门资讯
    public function topic() {
        $info = Topic::where(['hotel_id' => $this->hotel_id, 'tuijian' => 1, 'status' => 1])->orderBy('id', 'DESC')->first();
        return [
            'info'        => $info,
            'key'         => 'Topic',
            'name'        => '热门资讯',
            'relation_id' => 0,
        ];
    }

    // 热门评价
    public function assess() {
        $info = Assess::where(['hotel_id' => $this->hotel_id, 'recommend' => 1])->orderBy('id', 'DESC')->first();
        return [
            'info'        => $info,
            'key'         => 'assess',
            'name'        => '热门评价',
            'relation_id' => 0,
        ];
    }

    public function userCenterNav() {
        $list = UserCenterNav::where(['hotel_id' => $this->hotel_id, 'is_show' => 1])->orderBy('order', 'ASC')->get();
        return [
            'list'        => $list,
            'key'         => 'user_center_nav',
            'name'        => '用户中心导航图标',
            'relation_id' => 0,
            'row_num'     => 12
        ];
    }


    // 宣传片视频
    public function hotVideo() {

        return [];
    }

    // 小超市 商品分类
    public function getSmgGoodsCats(Request $request) {
        $where             = [];
        $where['hotel_id'] = $this->hotel_id;
        $list              = SmgGoodsCategory::where($where)->orderBy('sort', 'DESC')->get();
        return returnData(200, 1, ['list' => $list], 'ok');
    }

    // 小超市 商品列表
    public function getSmgGoodsLists(Request $request) {
        $where = ['hotel_id' => $this->hotel_id, 'putaway' => 1];
        if (!empty($request->cid)) {
            $where['cid'] = $request->cid;
        }
        $list = SmgGood::where($where)
            ->select(['id', 'cid', 'goods_name', 'goods_img', 'price', 'recommend'])
            ->orderBy('recommend', 'DESC')
            ->get();
        return returnData(200, 1, ['list' => $list], 'ok');
    }

    // 获取验证码
    public function getPhoneCode(Request $request) {
        $request->validate(
            [
                'phone' => 'required',
            ], [
                'phone.required' => '手机号码 不能为空',
            ]
        );
        $phone = $request->get('phone');
        $serv  = new \App\Services\SmsService();
        return $serv->send($phone);

    }


    // 各类协议
    public function getXieyi(Request $request) {
        $type     = $request->get('type');
        $hotel_id = $request->get('hotel_id');


        $data[$type] = Help::where(['hotel_id' => $hotel_id, 'title_as' => $type])->select('id', 'title', 'contents')->first();
        return returnData(200, 1, $data, 'ok');

        switch ($type) {
            case 'all':
                $data['user_service'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'user_service'])->select('id', 'title', 'contents')->first();//(new \App\Model\Article())->getData(['id' => 1], '', 'id,title,content'); //Article::where(['id' => 1])->value('content');
                $data['yinsi']        = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'yinsi'])->select('id', 'title', 'contents')->first();
                break;
            case 'user_service':
                $data['user_service'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'user_service'])->select('id', 'title', 'contents')->first();
                break;
            case 'yinsi':
                $data['yinsi'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'yinsi'])->select('id', 'title', 'contents')->first();
                break;
            case 'vipfuwu':
                $data['vipfuwu'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'vipfuwu'])->select('id', 'title', 'contents')->first();
                break;
            case 'dingfang_tips':
                $data['dingfang_tips'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'dingfang_tips'])->select('id', 'title', 'contents')->first();
                break;
            case 'geren_auth_shenming':
                $data['geren_auth_shenming'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'geren_auth_shenming'])->select('id', 'title', 'contents')->first();
                break;
            case 'hotel_service':
                $data['hotel_service'] = '123';//Article::where(['hotel_id' => $hotel_id, 'title_as' => 'hotel_service'])->select('id', 'title', 'contents')->first();
                break;
            default:
                $data['user_service']        = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'user_service'])->select('id', 'title', 'contents')->first();//(new \App\Model\Article())->getData(['id' => 1], '', 'id,title,content'); //Article::where(['id' => 1])->value('content');
                $data['yinsi']               = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'yinsi'])->select('id', 'title', 'contents')->first();
                $data['geren_auth_shenming'] = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'geren_auth_shenming'])->select('id', 'title', 'contents')->first();
                $data['hotel_service']       = Article::where(['hotel_id' => $hotel_id, 'title_as' => 'hotel_service'])->select('id', 'title', 'contents')->first();

                break;
        }
        return returnData(200, 1, $data, 'ok');
    }

    // 更新用户位置
    public function jisuanJuli(Request $request) {
        $form     = $request->get('from');
        $hotel_id = $request->get('hotel_id');
        //$to = $request->get('to');
        $form_arr = explode(',', $form);
        //$to_arr = explode(',',$to);
        if (empty($hotel_id)) {
            return returnData(205, 0, [], '计算距离失败1001');
        }
        $coordinates = Hotel::where(['id' => $hotel_id])->value('coordinates');
        $to_arr      = explode(',', $coordinates);
        if (empty($form_arr[0])) {
            return returnData(205, 0, [], '计算距离失败1002');
        }
        $res = $this->distance($form_arr[0], $form_arr[1], $to_arr[0], $to_arr[1]);

        return returnData(200, 1, ['juli' => $res], 'ok');
    }

    // 计算两个坐际的直线距离
    public function distance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // 地球半径，单位为公里

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // 计算两点之间的直线距禋，单位为公里

        return ceil($distance);
    }

    // 小程序 通过code 获取手机号

    /**Array
     * (
     * [errcode] => 0
     * [errmsg] => ok
     * [phone_info] => Array
     * (
     * [phoneNumber] => 17681849188
     * [purePhoneNumber] => 17681849188
     * [countryCode] => 86
     * [watermark] => Array
     * (
     * [timestamp] => 1713949746
     * [appid] => wx7246aea8d02dabdf
     * )
     *
     * )
     *
     * )
     * @desc
     * @param Request $request
     * author eRic
     * dateTime 2024-04-24 17:10
     */
    public function getPhoneNumber(Request $request) {
        $hotel_id    = $request->get('hotel_id');
        $code        = $request->get('code');
        $miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
        $res         = $miniProgram->base->getPhoneNumber($code);
        if (!empty($res['phone_info'])) {
            return returnData(200, 1, ['phoneNumber' => $res['phone_info']['phoneNumber']], 'ok');
        }
        $errormsg = !empty($res['errmsg']) ? $res['errmsg'] : '';
        return returnData(205, 0, [], '获取手机号失败:' . $errormsg);
    }

    // 获取配置信息
    public function getShopConfig(Request $request) {
        $data = $this->shopConfig();
        return returnData(200, 1, ['info' => $data], 'ok');
    }


}
