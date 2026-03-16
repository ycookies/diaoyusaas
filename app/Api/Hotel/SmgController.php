<?php

namespace App\Api\Hotel;

use App\Models\Hotel\Banner;
use App\Models\Hotel\DinnerGood;
use App\Models\Hotel\HomeNav;
use App\Models\Hotel\SmgGood;
use App\Models\Hotel\SmgGoodsCategory;
use App\Models\Hotel\Seller;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Requests\Request;

// 小超市
class SmgController extends BaseController {

    // 列表
    public function lists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $cid = $request->get('cid');
        $where = [];
        $where[] = ['hotel_id','=',$hotel_id];
        $where[] = ['putaway','=',1];
        if(!empty($cid)){
            $where[] = ['cid','=',$cid];
        }
        $list = SmgGood::where($where)
            ->orderBy('recommend','DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list),'ok');
    }

    // 详情
    public function detail(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $smg_id = $request->get('goods_id');
        $info = SmgGood::find($smg_id);
        return returnData(200, 1, ['info'=> $info],'ok');
    }

    // 分类列表

    /**
     * @desc
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cats(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $where[] = ['hotel_id','=',$hotel_id];
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 50);
        $list = SmgGoodsCategory::where($where)
            ->select(['id','hotel_id','icon','title','pid','level','desc','sort'])
            ->orderBy('id','DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list),'ok');
    }
}
