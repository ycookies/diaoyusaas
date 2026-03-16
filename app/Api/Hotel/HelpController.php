<?php

namespace App\Api\Hotel;

use Illuminate\Support\Facades\Auth;
use App\Admin;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\Help;
use App\Models\Hotel\HelpType;


/**
 * 帮助中心
 */
class HelpController extends BaseController
{

    // 获取分类
    public function getCats(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $parent_id = $request->get('parent_id', '0');
        $list = HelpType::where(['hotel_id' => $hotel_id, 'parent_id' => $parent_id])->get();
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取列表
    public function getLists(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);
        $type_id = $request->get('type_id');

        $where = [];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['status', '=', Help::Status1];
        $where[] = ['type_id', '=', $type_id];

        $list = Help::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取详情
    public function getDetail(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $art_id = $request->get('art_id');

        $info = Help::where(['hotel_id' => $hotel_id, 'id' => $art_id])->first();
        return returnData(200, 1, ['info' => $info], 'ok');
    }
}
