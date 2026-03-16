<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\User;
use App\Models\Hotel\UserMember;
use App\Models\Hotel\UserLevel;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Modal;
use Illuminate\Http\Request;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form as WidgetForm;
use App\Models\Hotel\WxCardTpl;

// 用户各项处理
class UserHandleController extends AdminController {

    // 用户会员卡挂失
    public function cardUnavailable(Request $request){
        $hotel_id = Admin::user()->hotel_id;
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            //'card_id' => 'required',
            'card_code' => 'required',
            //'reason' => 'required',
        ],[
            'user_id.required' => '用户ID 不能为空',
            //'card_id.required' => '卡券ID 不能为空',
            'card_code.required' => '用户会员卡号 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $card_id  = WxCardTpl::where(['hotel_id'=> $hotel_id])->value('card_id');
        if(empty($card_id)){
            return (new WidgetForm())->response()->error('未获取到卡券ID');
        }
        $user_id = $request->get('user_id');
        //$card_id = $request->get('card_id');
        $code = $request->get('card_code');
        $reason = $request->get('reason');

        $wxOpen     = app('wechat.open');
        $gzhobj     = $wxOpen->hotelWxgzh($hotel_id);
        $res = $gzhobj->card->code->disable($code,$card_id);
        addlogs('card_code_disable',[$code,$card_id],$res);
        if(isset($res['errcode']) && $res['errcode'] == 0){
            // 更新用户信息
            User::where(['id'=> $user_id])->update(['card_code'=> '']);
            return (new WidgetForm())->response()->success('挂失成功')->refresh();
        }

        $error_msg = !empty($res['errmsg']) ? $res['errmsg']:'-';
        return JsonResponse::make()->data($res)->error('挂失失败:'.$error_msg);
    }
}
