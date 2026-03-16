<?php

namespace App\Api\Hotelold;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;

class IndexController extends BaseController {

    // 获取首页配置信息
    public function index(Request $request) {
        //$this->user              = Auth::guard('api')->user();
        $home_pages['search']    = $this->search();
        $home_pages['banner']    = $this->banner();
        $home_pages['nav']       = $this->nav();
        $home_pages['notice']    = $this->notice();
        $home_pages['hotelinfo'] = $this->hotelInfo();
        $home_pages['topRoom']   = $this->topRoom();
        $home_pages['goods']     = $this->goods();
        return returnData(200, 1, ['home_pages' => $home_pages]);
    }

    // 酒店信息
    public function hotelInfo() {
        $hotelinfo = Seller::where('user_id', $this->seller['seller_id'])->first();
        return [
            'hotel_name'    => $hotelinfo->name,
            'hotel_address' => $hotelinfo->address,
            'hotel_tel'     => $hotelinfo->tel,
            'coordinates'   => $hotelinfo->coordinates,
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
        $list = Banner::where(['seller_id' => $this->seller['seller_id']])->get();

        //$banners = $list;
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
        $list = HomeNav::where(['seller_id' => $this->seller['seller_id']])->get();
        return [
            'list'        => $list,
            'key'         => 'home_nav',
            'name'        => '导航图标',
            'relation_id' => 0,
            'row_num'     => 4
        ];
    }

    /**
     * @desc  推荐房型
     * @return array
     */
    public function topRoom() {
        $list = Room::where(['seller_id' => $this->seller['seller_id'], 'recommend' => 1])->select(['id', 'name', 'price', 'logo'])->orderBy('sort', 'DESC')->limit(4)->get();
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
        return [
            'is_edit'           => '1',
            'key'               => 'notice',
            'name'              => '公告',
            'notice_bg_color'   => '#ED7E78',
            'notice_content'    => '旅忆行开源系统-助力酒旅行业提升服务品质',
            'notice_name'       => '旅忆行',
            'notice_text_color' => '#FFFFFF',
            'notice_url'        => 'https://xdmall.oss-cn-zhangjiakou.aliyuncs.com/uploads/mall8/20200630/f6af069b21630a9c42539e761381f13a.png',
            'relation_id'       => 0,
        ];
    }

    // 弹窗广告
    public function modals() {
        return [];
    }

    // 热门活动
    public function activity() {
        return [];
    }

    // 热门商品
    public function goods() {
        $list = DinnerGood::where(['seller_id' => $this->seller['seller_id'], 'recommend' => 1])->select(['id', 'goods_name', 'goods_img', 'price'])->limit(4)->get();
        return [
            'list'        => $list,
            'key'         => 'goods',
            'name'        => '热门商品',
            'relation_id' => 0,
            'row_num'     => 4
        ];
    }

    // 热门资讯
    public function news() {
        return [];
    }

    // 热门评论
    public function comment() {
        return [];
    }

    // 宣传片视频
    public function hotVideo() {
        return [];
    }


}
