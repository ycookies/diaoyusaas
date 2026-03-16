<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\OftenLvkeinfo;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 常用信息
 */
class CommonInfoController extends BaseController {

    // 列表
    public function getLvkeinfoList(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $list     = OftenLvkeinfo::where(['hotel_id' => $hotel_id, 'user_id' => $user->id])->get();
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 详情
    public function getLvkeinfoDetail(Request $request) {
        $user        = JWTAuth::parseToken()->authenticate();
        $hotel_id    = $request->get('hotel_id');
        $lvkeinfo_id = $request->get('lvkeinfo_id');

        $info = OftenLvkeinfo::where(['id' => $lvkeinfo_id, 'hotel_id' => $hotel_id])->first();
        if (!$info) {
            return returnData(205, 0, [], '未找到此常用旅客信息');
        }
        $info->is_benren = !empty($info->is_benren) ? true:false;
        $info->is_default = !empty($info->is_default) ? true:false;
        return returnData(200, 1, ['info' => $info], 'ok');
    }

    // 获取默认常用旅客
    public function getLvkeinfoDefault(Request $request) {
        $user        = JWTAuth::parseToken()->authenticate();
        $lvke_id = $request->get('lvke_id','');
        $hotel_id    = $request->get('hotel_id');
        if(!empty($lvke_id)){
            $where = ['user_id' => $user->id, 'id' => $lvke_id];
        }else{
            $where = ['user_id' => $user->id, 'hotel_id' => $hotel_id,'is_benren'=>1];
        }

        $info = OftenLvkeinfo::where($where)->orderBy('id', 'DESC')->first();
        if (!$info) {
            return returnData(205, 0, [], '未找到此常用旅客信息');
        }

        return returnData(200, 1, ['info' => $info], 'ok');
    }

    /**
     * 创建/更新常住旅客信息
     * @desc 创建/更新常住旅客信息
     */
    public function actionLvkeinfo(Request $request) {
        $user        = JWTAuth::parseToken()->authenticate();
        $user_id     = $user->id;
        $lvkeinfo_id = $request->get('lvkeinfo_id');
        $hotel_id    = $request->get('hotel_id');
        if (!empty($lvkeinfo_id)) {
            $info = OftenLvkeinfo::where(['id' => $lvkeinfo_id,'user_id' => $user_id, 'hotel_id' => $hotel_id])->first();
            if (!$info) {
                return returnData(205, 0, [], '未找到此常用旅客信息');
            }
        }
        // 校验数据
        $request->validate(
            [
                'hotel_id'    => 'required',
                'zname'       => 'required|',
                'phone'       => ['required', '', 'regex:/^1[3-9]\d{9}$/'],
                'exin'        => 'nullable|max:20',
                'ename'       => 'nullable|max:20',
                'idcard'      => 'nullable|size:18',
                'idcard_date' => 'nullable|date',
            ], [
                'hotel_id.required'    => '酒店ID 不能为空',
                'zname.required'       => '中文名字 不能为空',
                'phone.required'       => '手机号码 不能为空',
                'phone.regex'          => '手机号码 格式不正确',
                'exin.max'             => '英文姓 不能为空',
                'ename.max'            => '英文名 不能为空',
                'idcard.size'          => '身份证号码必须是18位',
                'idcard_date.required' => '证件有效期 格式不正确',
            ]
        );
        $insdata = [
            'hotel_id'    => $hotel_id,
            'user_id'     => $user_id,
            'zname'       => $request->zname,
            'phone'       => $request->phone,
            'exin'        => $request->exin,
            'ename'       => $request->ename,
            'idcard'      => $request->idcard,
            'idcard_date' => $request->idcard_date,
            'is_benren'   => $request->is_benren,
            'is_default'  => $request->is_default,
        ];
        $insdata = array_filter($insdata);
        if($request->is_default){
            OftenLvkeinfo::where(['id' => $lvkeinfo_id, 'hotel_id' => $hotel_id])->update(['is_benren' => 0]);
        }
        // 更新操作
        if (!empty($lvkeinfo_id)) {
            OftenLvkeinfo::createOrUpdate(['id' => $lvkeinfo_id,'user_id' => $user_id,'hotel_id' => $hotel_id],$insdata);
        }else{
            OftenLvkeinfo::createOrUpdate(['user_id' => $user_id,'zname'=> $request->zname,'hotel_id' => $hotel_id],$insdata);
        }


        /*if (!empty($lvkeinfo_id)) {

            OftenLvkeinfo::where(['id' => $lvkeinfo_id, 'hotel_id' => $hotel_id])->update($insdata);
        } else {
            // 新增操作
            $status = OftenLvkeinfo::create($insdata);
        }*/

        return returnData(200, 1, ['id' => ''], 'ok');
    }

}
