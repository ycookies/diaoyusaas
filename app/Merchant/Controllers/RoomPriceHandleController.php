<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Hotel\RoompriceMember;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoompriceAgreement;
use App\Models\Hotel\RoompriceRetail;

// 客房价格维护处理
class RoomPriceHandleController extends Controller {

    /**
     * 日历价格设置
     */
    public function calendarPriceSave(Request $request) {
        $user      = Admin::user();
        $validator = \Validator::make($request->all(), [
            'room_id' => 'required|numeric',
            'days'    => 'required',
            'type'    => 'required',
            'price'   => 'required|numeric',
            //'total_num'   => 'required|numeric',
        ], [
            'room_id.required' => '客房ID 不能为空',
            'room_id.numeric'  => '客房ID 只能是数字',
            'days.required'    => '日期 不能为空',
            'type.required'    => '价格类型 不能为空',
            'price.required'   => '价格 不能为空',
            'price.numeric'    => '价格 只能是数字',
            'total_num.required'   => '库存 不能为空',
            'total_num.numeric'    => '库存 只能是数字',
        ]);
        if ($validator->fails()) {
            return JsonResponse::make()->error($validator->errors()->first());
        }
        $price = $request->get('price');
        $room_id = $request->get('room_id');
        $type = $request->get('type');
        $open_status = $request->get('open_status');
        //$total_num = $request->get('total_num');
        $days = $request->get('days');
        $days = str_replace('days_','',$days);
        $days = str_replace('_','-',$days);
        // 检查数据合法性
        if (!preg_match('/^[0-9]+(.[0-9]{2})?$/', $price)) {
            return JsonResponse::make()->error('价格格式不正确,最多两位小数');
        }

        Roomprice::addDaysPice($user->hotel_id,$room_id,$type,$days,$price,$open_status);

        return JsonResponse::make()->success('保存成功');
    }

    /**
     * 房型房价sku日历价格设置
     */
    public function calendarSkuPriceSave(Request $request) {
        $user      = Admin::user();
        $validator = \Validator::make($request->all(), [
            'room_id' => 'required|numeric',
            'room_sku_id' => 'required|numeric',
            'days'    => 'required',
            'type'    => 'required',
            'price'   => 'required|numeric',
            //'total_num'   => 'required|numeric',
        ], [
            'room_id.required' => '客房ID 不能为空',
            'room_id.numeric'  => '客房ID 只能是数字',
            'room_sku_id.required'  => '客房sku_ID 只能是数字',
            'room_sku_id.numeric'  => '客房sku_ID 只能是数字',
            'days.required'    => '日期 不能为空',
            'type.required'    => '价格类型 不能为空',
            'price.required'   => '价格 不能为空',
            'price.numeric'    => '价格 只能是数字',
            'total_num.required'   => '库存 不能为空',
            'total_num.numeric'    => '库存 只能是数字',
        ]);
        if ($validator->fails()) {
            return JsonResponse::make()->error($validator->errors()->first());
        }
        $price = $request->get('price');
        $room_id = $request->get('room_id');
        $room_sku_id = $request->get('room_sku_id');
        $type = $request->get('type');
        $open_status = $request->get('open_status');
        //$total_num = $request->get('total_num');
        $days = $request->get('days');
        $days = str_replace('days_','',$days);
        $days = str_replace('_','-',$days);
        // 检查数据合法性
        if (!preg_match('/^[0-9]+(.[0-9]{2})?$/', $price)) {
            return JsonResponse::make()->error('价格格式不正确,最多两位小数');
        }

        Roomprice::addSkuDaysPice($user->hotel_id,$room_id,$room_sku_id,$type,$days,$price,$open_status);

        return JsonResponse::make()->success('保存成功');
    }

}
