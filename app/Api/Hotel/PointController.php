<?php

namespace App\Api\Hotel;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\IntegralLog;
use Illuminate\Support\Arr;

// 积分管理
class PointController extends BaseController {

    // 积分列表
    public function getPointLists(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $where = [
            'user_id' => $user->id,
            'hotel_id' => $hotel_id,
        ];
        $list     = IntegralLog::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 积分详情
    public function getPointDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);

        $list     = User::where(['temp_parent_id' => $user->id])
            ->orWhere(['parent_id' => $user->id])
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }
}
