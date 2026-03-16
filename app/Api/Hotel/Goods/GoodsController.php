<?php

namespace App\Api\Hotel\Goods;

use App\Api\Hotel\BaseController;
use App\Models\Hotel\Goods\Good;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\PriceCalculator;

// 全局商品
class GoodsController extends BaseController {

    // 获取商品价格支付相关信息
    public function getGoodsPayPriceInfo(Request $request){
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id      = $request->get('hotel_id');
        $goods_id = $request->get('goods_id');
        if(empty($goods_id)){
            return returnData(404, 0, [], '商品ID不能为空');
        }
        $goods_info  =  Good::where(['id'=> $goods_id])->first();

        if (!$goods_info) {
            return returnData(404, 0, [], '找不到订单信息');
        }

        $total_price = bcmul($goods_info->price, 1, 2);
        $total_price = formatFloat($total_price);

        /*$user_id = $user->id;
        $total_price = $goods_info->price;
        $calculator = new \App\Services\PriceCalculator($total_price);
        $calculator->calculateFinalPrice($user_id);

        // 获取最终价格和折扣信息
        $finalPrice = $calculator->getFinalPrice();
        $youhui_price = $calculator->getYouhuiPrice();
        $discounts = $calculator->getDiscounts();

        $data        = [
            'youhui_price' => $youhui_price,
            'yuan_price'   => $total_price,
            'total_price'  => $finalPrice,
            'coupon_list'  => $discounts,
        ];*/

        $data        = [
            'youhui_price' => '',
            'yuan_price'   => $total_price,
            'total_price'  => $total_price,
            'coupon_list'  => [],
        ];
        return returnData(200, 1, ['pay_price_info' => $data], 'ok');



    }

}
