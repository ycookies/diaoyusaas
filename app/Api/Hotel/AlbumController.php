<?php

namespace App\Api\Hotel;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\Album;
use App\Models\Hotel\AlbumGroup;

/**
 * 相册
 */
class AlbumController extends BaseController {

    // 获取列表
    public function getLists(Request $request){
        $hotel_id = $request->get('hotel_id');
        $album_group_id = $request->get('album_group_id');
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 10);

        $where = [];
        $where[] = ['hotel_id','=', $hotel_id];
        $where[] = ['status','=', 1];
        if(!empty($album_group_id)){
            $where[] = ['album_group_id' ,'=', $album_group_id];
        }
        $list = AlbumGroup::with('album')
            ->where($where)->get();

        /*$list = Album::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);*/

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取详情
    public function getDetail(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $art_id = $request->get('album_id');
        $info = Album::where(['hotel_id'=> $hotel_id,'id' => $art_id])->first();
        return returnData(200, 1, ['info' => $info], 'ok');

    }
}
