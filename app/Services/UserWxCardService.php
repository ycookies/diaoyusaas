<?php

namespace App\Services;

use App\Models\Hotel\WxCardTpl;
use App\User;

/**
 * 用户微信会员卡 同步数据
 * @package App\Services
 * anthor Fox
 */
class UserWxCardService extends BaseService {

    public $gzhobj;

    // 增加余额
    public function addBalance($user_id, $add_balance, $record_balance) {
        $user_info = User::find($user_id);
        $card_id   = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->value('card_id');
        if (empty($card_id)) {
            return false;
        }
        $info   = [
            'code'           => $user_info->card_code, //卡券Code码。
            'card_id'        => $card_id, //卡券ID。
            'balance'        => bcmul($user_info->balance ,100,0), //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
            'add_balance'    => bcmul($add_balance ,100,0),//$add_balance,
            'record_balance' => $record_balance, //商家自定义金额消耗记录，不超过14个汉字。
        ];
        $wxOpen = app('wechat.open');
        //$hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj = $wxOpen->hotelWxgzh($user_info->hotel_id);
        $result = $gzhobj->card->member_card->updateUser($info);
        addlogs('member_card_updateUser_'.__FUNCTION__, $info, $result,$user_info->hotel_id);
        return $result;
    }

    // 减少余额
    public function cutBalance($user_id, $cut_balance, $record_balance) {
        $user_info = User::find($user_id);
        $card_id   = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->value('card_id');
        if (empty($card_id)) {
            return false;
        }
        $info   = [
            'code'           => $user_info->card_code, //卡券Code码。
            'card_id'        => $card_id, //卡券ID。
            'balance'        => bcmul($user_info->balance ,100,0), //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
            'add_balance'    => - bcmul($cut_balance ,100,0),
            'record_balance' => $record_balance, //商家自定义金额消耗记录，不超过14个汉字。
        ];
        $wxOpen = app('wechat.open');
        //$hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj = $wxOpen->hotelWxgzh($user_info->hotel_id);
        $result = $gzhobj->card->member_card->updateUser($info);
        addlogs('member_card_updateUser_'.__FUNCTION__, $info, $result,$user_info->hotel_id);
        return $result;
    }

    // 增加积分
    public function addPoint($user_id, $add_point, $record_bonus) {
        $user_info = User::find($user_id);
        $card_id   = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->value('card_id');
        if (empty($card_id)) {
            return false;
        }
        $info   = [
            'code'         => $user_info->card_code, //卡券Code码。
            'card_id'      => $card_id, //卡券ID。
            'bonus'        => $user_info->point,
            'add_bonus'    => $add_point,
            'record_bonus' => $record_bonus,
        ];
        $wxOpen = app('wechat.open');
        //$hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj = $wxOpen->hotelWxgzh($user_info->hotel_id);
        $result = $gzhobj->card->member_card->updateUser($info);
        addlogs('member_card_updateUser_'.__FUNCTION__, $info, $result,$user_info->hotel_id);
        return $result;
    }

    // 减少积分
    public function cutPoint($user_id, $cut_point, $record_bonus) {
        $user_info = User::find($user_id);
        $card_id   = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->value('card_id');
        if (empty($card_id)) {
            return false;
        }
        $info   = [
            'code'         => $user_info->card_code, //卡券Code码。
            'card_id'      => $card_id, //卡券ID。
            'bonus'        => $user_info->point, //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
            'add_bonus'    => -$cut_point,
            'record_bonus' => $record_bonus, //商家自定义金额消耗记录，不超过14个汉字。
        ];
        $wxOpen = app('wechat.open');
        //$hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj = $wxOpen->hotelWxgzh($user_info->hotel_id);
        $result = $gzhobj->card->member_card->updateUser($info);
        addlogs('member_card_updateUser_'.__FUNCTION__, $info, $result,$user_info->hotel_id);
        return $result;
    }

    // 更新等级
    public function uplevel($user_id, $cut_point, $record_bonus) {
        $user_info = User::find($user_id);
        $card_id   = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->value('card_id');
        if (empty($card_id)) {
            return false;
        }
        $info   = [
            'code'         => $user_info->card_code, //卡券Code码。
            'card_id'      => $card_id, //卡券ID。
            'bonus'        => $user_info->point, //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
            'add_bonus'    => -$cut_point,
            'record_bonus' => $record_bonus, //商家自定义金额消耗记录，不超过14个汉字。
        ];
        $wxOpen = app('wechat.open');
        //$hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj = $wxOpen->hotelWxgzh($user_info->hotel_id);
        $result = $gzhobj->card->member_card->updateUser($info);
        addlogs('member_card_updateUser_'.__FUNCTION__, $info, $result,$user_info->hotel_id);
        return $result;
    }
}