<?php

namespace App\Api\Hotelold;

use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\WxappConfig;
use App\Services\WxUserService;
use EasyWeChat\Factory;
use Illuminate\Http\Request as apiRequest;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DinnerGoodsResource;
class DinnerGoodController extends BController {

    // 商品列表
    public function lists(apiRequest $request){
        $seller_id = $request->seller_id;
        $user          = $request->user();
        $pagesize      = empty($request->pagesize) || $request->pagesize > 100 ? 20 : $request->pagesize;

        $where = [];
        $where[] = ['seller_id','=',$seller_id];
        $where[] = ['putaway','=',1]; // 上架的商品
        $list = DinnerGood::where($where)
            ->select(['id','cid','goods_name','goods_img','price','desc','sales_volume','putaway'])
            ->orderBy('recommend','DESC')
            ->paginate($pagesize);
        $data = $this->pageintes($list, $pagesize, DinnerGoodsResource::collection($list));

        apiReturn(200, 1, $data, '');
    }

    // 商品详情
    public function detail(apiRequest $request,$id){
        $seller_id = $request->seller_id;
        $where = [];
        $where[] = ['seller_id','=',$seller_id];
        $where[] = ['putaway','=',1]; // 上架的商品
        $where[] = ['id','=',$id]; //
        $list = DinnerGood::where($where)->select(['id','cid','goods_name','goods_img','price','desc','sales_volume','putaway'])->orderBy('recommend','DESC')->get();
        $data['list'] = $list;
        return returnData(200,1,$data);
    }
}
