<?php

namespace App\Api\Hotel\Tuangou;

use App\Models\Hotel\Goods\Good;
use App\Models\Hotel\Tuangou\TuangouGoods;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Api\Hotel\BaseController;
// 团购商品
class TuangouGoodsController extends BaseController {

    // 获取商品列表
    public function goodsList(Request $request){
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $is_home = $request->get('is_home');
        $hotel_id = $request->get('hotel_id');
        $list = TuangouGoods::with('goods','warehouse')
            ->where(['hotel_id'=> $hotel_id])
            ->whereHas('goods', function ($query)  {
                $query->where('status', 1);
            })
            ->orderBy('sorts','DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取商品详情
    public function goodsDetail(Request $request){
        $hotel_id              = $request->get('hotel_id');
        $tuangou_goods_id               = $request->get('tuangou_goods_id');
        $info = TuangouGoods::with('goods','warehouse','goods.purchaseNotices','goods.serviceGuarantees')
            ->where([
                'id'=> $tuangou_goods_id,
                'hotel_id'=> $hotel_id,
            ])
            ->first();

        $carousel_list = [];
        // 组装轮播图片列表
        if(!empty($info->warehouse->video_url)){
            $carousel_list[] = [
                'image' => $info->warehouse->video_url,
                'type' => 'video',
            ];
        }
        $pic_img = json_decode($info->warehouse->pic_url,true);

        foreach ($pic_img as $key => $imgsrc) {
            $carousel_list[] = [
                'image' => $imgsrc,
                'type' => 'image',
            ];
        }



        $info->carousel_list = $carousel_list;
        if(!$info){
            return returnData(404, 0, [], '找不到商品信息');
        }
        if($info->goods->status == 0){
            return returnData(403, 0, [], '商品已下架');
        }
        /*if($info->goods->is_level == 1){
            $price_arr = (new \App\Services\UserPriceService())->getMemberPrice($user->id,$info->goods->price);
            $info->goods->price = $price_arr['discount_price'];
        }*/
        return returnData(200, 1, ['info' => $info], 'ok');
    }

}
