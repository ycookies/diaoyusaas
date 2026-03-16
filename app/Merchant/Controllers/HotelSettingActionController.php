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

use App\Models\Hotel\HotelSetting;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// 列表
class HotelSettingActionController extends Controller {
    /**
     * @desc 参数存改主入口
     */
    public function edit(Request $request) {
        $action_name = $request->get('action_name');
        $validator   = \Validator::make($request->all(), [
            'action_name' => 'required',
        ], [
            'action_name.required' => '操作项 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        // 根据操作名不同 调用不用的函数做参数验证，存改
        switch ($action_name) {
            case 'booking_configs':
                return $this->booking_configs($request);
            case 'booking_notify':
                return $this->booking_notify($request);
                break;
            case 'booking_switch':
                return $this->booking_switch($request);
                break;
            case 'user_level_configs':
                return $this->user_level_configs($request);
                break;
            case 'share_poster':
                return $this->share_poster($request);
                break;
            case 'recharge_package':
                return $this->recharge_package($request);
                break;
            case 'parking_config':
                return $this->parking_config($request);
                break;
            case 'user_reward_config':
                return $this->user_reward_config($request);
                break;
            case 'minapp_msg_tpl':
                return $this->minapp_msg_tpl($request);
                break;
            case 'gzh_msg_tpl':
                return $this->gzh_msg_tpl($request);
                break;
            case 'tuangou':
                return $this->tuangou($request);
                break;
            case 'general_settings':
                return $this->general_settings($request);
                break;
            default:
                break;
        }
        return (new WidgetForm())->response()->error('保存时遇到问题，请检查!(没有此项操作[' . $action_name . ']的处理方法)');

    }


    // 上传文件
    public function upimgs(Request $request) {

    }

    // 客房预订配置
    public function booking_configs(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'              => 'required',
            'booking_full_status'   => 'required',
            'cancelling_time'       => 'required',
            'vip_cancelling_time'   => 'required',
            'is_cancelling_verify'  => 'required',
            'booking_wait_pay_time' => 'required',
            //'booking_notify_gzh_open_id' => 'required',
            //'max_booking_days_num' => 'nullable|numeric',
            //'max_room_price_set_num' => 'nullable|numeric',
        ], [
            'hotel_id.required'              => '酒店ID 不能为空',
            'is_cancelling_verify.required'  => '请选择 是否审核',
            'booking_full_status.required'   => '订房全局关闭 不能为空',
            'cancelling_time.required'       => '请选择 普卡会员 退订规则',
            'vip_cancelling_time.required'   => '请选择 付费VIP会员 退订规则',
            'booking_wait_pay_time.required' => '请填写 订房未支付超时时间',
            //'max_booking_days_num.numeric' => '用户可预定最大天数 只能是数字',
            //'max_booking_days_num.required' => '用户可预定最大天数 不能为空',
            //'max_room_price_set_num.numeric' => '酒店客房维护的最大天数 只能是数字',
            //'max_room_price_set_num.required' => '酒店客房维护的最大天数 不能为空',

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'booking_full_status'   => $request->booking_full_status,
            'is_cancelling_verify'  => $request->is_cancelling_verify,
            'cancelling_time'       => $request->cancelling_time,
            'vip_cancelling_time'   => $request->vip_cancelling_time,
            'booking_wait_pay_time' => $request->booking_wait_pay_time,
        ];
        if (isset($request->exceed_cancelling_time_rate_24) && $request->exceed_cancelling_time_rate_24 != '') {
            $insdata['exceed_cancelling_time_rate_24'] = $request->exceed_cancelling_time_rate_24;
        }
        if (isset($request->exceed_cancelling_time_rate_48) && $request->exceed_cancelling_time_rate_48 != '') {
            $insdata['exceed_cancelling_time_rate_48'] = $request->exceed_cancelling_time_rate_48;
        }
        if (isset($request->vip_exceed_cancelling_time_rate_24) && $request->vip_exceed_cancelling_time_rate_24 != '') {
            $insdata['vip_exceed_cancelling_time_rate_24'] = $request->vip_exceed_cancelling_time_rate_24;
        }
        if (isset($request->vip_exceed_cancelling_time_rate_48) && $request->vip_exceed_cancelling_time_rate_48 != '') {
            $insdata['vip_exceed_cancelling_time_rate_48'] = $request->vip_exceed_cancelling_time_rate_48;
        }
        $hotel_id    = $request->get('hotel_id');
        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 订房通知对象配置
    public function booking_notify(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'                      => 'required',
            'booking_notify_phone'          => 'required',
            //'booking_notify_gzh_open_id' => 'required',
            'booking_notify_qywx_robot_url' => 'nullable|url',
            'booking_notify_mail'           => 'nullable|email',
            'kefu_center_qywx_robot_url'    => 'nullable|url',
        ], [
            'hotel_id.required'                      => '酒店ID 不能为空',
            'booking_notify_phone.required'          => '接受订房通知短信的手机号 不能为空',
            //'booking_notify_gzh_open_id.required' => '',
            'booking_notify_qywx_robot_url.required' => '企业微信群机器人 必须是网址',
            'booking_notify_mail.email'              => '接受订房通知的邮箱 格式不正确',
            'kefu_center_qywx_robot_url.required'    => '客服通知 企业微信群机器人 必须是网址',

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $booking_notify_phone      = $request->get('booking_notify_phone');
        $booking_notify_phone_arr  = explode(',', $booking_notify_phone);
        $booking_notify_phone_diff = array_flip(array_flip($booking_notify_phone_arr));
        if (count($booking_notify_phone_arr) != count($booking_notify_phone_diff)) {
            return (new WidgetForm())->response()->error('接受订房通知短信的手机号 不可有重复的手机号');
        }
        if (count($booking_notify_phone_arr) > 3) {
            return (new WidgetForm())->response()->error('接受订房通知短信的手机号 最多只支持3个手机号');
        }
        foreach ($booking_notify_phone_arr as $phone) {
            if (!isMobile($phone)) {
                return (new WidgetForm())->response()->error('接受订房通知短信的手机号 格式不正确');
            }
        }

        $insdata  = [
            'booking_notify_phone'          => $request->booking_notify_phone,
            'booking_notify_gzh_open_id'    => $request->booking_notify_gzh_open_id,
            'booking_notify_mail'           => $request->booking_notify_mail,
            'booking_notify_qywx_robot_url' => $request->booking_notify_qywx_robot_url,
            'kefu_center_qywx_robot_url'    => $request->kefu_center_qywx_robot_url,
        ];
        $hotel_id = $request->get('hotel_id');

        $sts = HotelSetting::createRow($insdata, $hotel_id);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    public function booking_switch(Request $request) {

    }

    // 用户等级配置
    public function user_level_configs(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'             => 'required',
            'user_level_rule_decs' => 'required',
            //'booking_notify_gzh_open_id' => 'required',
            //'max_booking_days_num' => 'nullable|numeric',
            //'max_room_price_set_num' => 'nullable|numeric',
        ], [
            'hotel_id.required'             => '酒店ID 不能为空',
            'user_level_rule_decs.required' => '会员等级规则说明 不能为空',
            //'max_booking_days_num.numeric' => '用户可预定最大天数 只能是数字',
            //'max_booking_days_num.required' => '用户可预定最大天数 不能为空',
            //'max_room_price_set_num.numeric' => '酒店客房维护的最大天数 只能是数字',
            //'max_room_price_set_num.required' => '酒店客房维护的最大天数 不能为空',

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata     = [
            'user_level_rule_decs' => $request->user_level_rule_decs,
            //'max_booking_days_num'           => $request->max_booking_days_num,
            //'max_room_price_set_num'       => $request->max_room_price_set_num,
        ];
        $hotel_id    = $request->get('hotel_id');
        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 分享海报
    public function share_poster(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'            => 'required',
            'share_poster_bg_img' => 'required',
        ], [
            'hotel_id.required'            => '酒店ID 不能为空',
            'share_poster_bg_img.required' => '背景图 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata     = [
            'share_poster_bg_img' => $request->share_poster_bg_img,
        ];
        $hotel_id    = $request->get('hotel_id');
        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }


    // 充值套餐包
    public function recharge_package(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'              => 'required',
            'recharge_package_list' => 'required',
        ], [
            'hotel_id.required'              => '酒店ID 不能为空',
            'recharge_package_list.required' => '请填写套餐包信息',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $recharge_package_list = $request->recharge_package_list;
        if (is_array($recharge_package_list)) {
            $recharge_package_list = json_encode($recharge_package_list, JSON_UNESCAPED_UNICODE);
        }
        $insdata     = [
            'recharge_package_list' => $recharge_package_list,
        ];
        $hotel_id    = $request->get('hotel_id');
        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 停车场配置信息
    public function parking_config(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'  => 'required',
            'parkingNo' => 'required',
        ], [
            'hotel_id.required'  => '酒店ID 不能为空',
            'parkingNo.required' => '请填写停车场编号',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata  = [
            'parkingNo' => $request->parkingNo,
        ];
        $hotel_id = $request->get('hotel_id');

        $re = HotelSetting::where(['field_key' => 'parkingNo', 'field_value' => $request->parkingNo])->first();
        if (!empty($re->hotel_id) && $re->hotel_id != $hotel_id) {
            JsonResponse::make()->data($request->all())->error('相同的停车场编号已经存在,请检查');
        }

        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 用户奖励配置
    public function user_reward_config(Request $request) {

        $validator = \Validator::make($request->all(), [
            'hotel_id'              => 'required',
            'user_card_kaika_point' => 'required',
            'user_booking_point'    => 'required',
            'user_pingjia_point'    => 'required',
            'user_share_valid_days' => 'required',
            'user_share_balance'    => 'required',
        ], [
            'hotel_id.required'              => '酒店ID 不能为空',
            'user_card_kaika_point.required' => '用户注册新开会员普卡奖励 不能为空',
            'user_booking_point.required'    => '用户成功预订酒店 奖励 不能为空',
            'user_pingjia_point.required'    => '用户住店给予评价 奖励 不能为空',
            'user_share_valid_days.required' => '用户推广邀请好友奖励条件 多少天 不能为空',
            'user_share_balance.required'    => '用户推广邀请好友成功住店 奖励余额 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata     = [
            'user_card_kaika_point' => $request->user_card_kaika_point,
            'user_booking_point'    => $request->user_booking_point,
            'user_pingjia_point'    => $request->user_pingjia_point,
            'user_share_valid_days' => $request->user_share_valid_days,
            'user_share_balance'    => $request->user_share_balance,
        ];
        $hotel_id    = $request->get('hotel_id');
        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！')->refresh();
    }

    // 小程序订阅模板消息
    public function minapp_msg_tpl(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'  => 'required',
            'parkingNo' => 'required',
        ], [
            'hotel_id.required'  => '酒店ID 不能为空',
            'parkingNo.required' => '请填写停车场编号',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata  = [
            'parkingNo' => $request->parkingNo,
        ];
        $hotel_id = $request->get('hotel_id');

        $re = HotelSetting::where(['field_key' => 'parkingNo', 'field_value' => $request->parkingNo])->first();
        if (!empty($re->hotel_id) && $re->hotel_id != $hotel_id) {
            JsonResponse::make()->data($request->all())->error('相同的停车场编号已经存在,请检查');
        }

        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 微信公众号模板消息
    public function gzh_msg_tpl(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'                    => 'required',
            'booking_gzh_msg_tpl_success' => 'required',
            'booking_gzh_msg_tpl_cancel'  => 'required',
            'booking_gzh_msg_tpl_fail'    => 'required',

        ], [
            'hotel_id.required'                    => '酒店ID 不能为空',
            'booking_gzh_msg_tpl_success.required' => '房型预定成功通知 模板ID 不能为空',
            'booking_gzh_msg_tpl_cancel.required'  => '预订取消通知 模板ID 不能为空',
            'booking_gzh_msg_tpl_fail.required'    => '预订失败通知 模板ID 不能为空',

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata  = [
            'booking_gzh_msg_tpl_success' => $request->booking_gzh_msg_tpl_success,
            'booking_gzh_msg_tpl_cancel'  => $request->booking_gzh_msg_tpl_cancel,
            'booking_gzh_msg_tpl_fail'    => $request->booking_gzh_msg_tpl_fail,
        ];
        $hotel_id = $request->get('hotel_id');

        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('保存成功！');
    }

    // 团购设置
    public function tuangou(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'                    => 'required',
            'is_tuangou_refund_verify' => 'required',
        ], [
            'hotel_id.required'                    => '酒店ID 不能为空',
            'is_tuangou_refund_verify.required' => '请选择是否开启退款审核',

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata  = [
            'is_tuangou_refund_verify' => $request->is_tuangou_refund_verify,

        ];
        $hotel_id = $request->get('hotel_id');

        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('保存成功！');
    }

    // 常规设置
    public function general_settings(Request $request) {
        $validator = \Validator::make($request->all(), [
            'hotel_id'                    => 'required',
            'user_regiser_required_wxcard' => 'required',
            'user_update_info_required' => 'required',
        ], [
            'hotel_id.required'                    => '酒店ID 不能为空',
            'user_regiser_required_wxcard.required' => '请选择 注册必须开卡 状态',
            'user_update_info_required.required' => '请选择 更新个人资料 状态'

        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $insdata  = [
            'user_regiser_required_wxcard' => $request->user_regiser_required_wxcard,
            'user_update_info_required' => $request->user_update_info_required,

        ];
        $hotel_id = $request->get('hotel_id');

        $action_name = $request->get('action_name');
        $sts         = HotelSetting::createRow($insdata, $hotel_id, $action_name);
        return JsonResponse::make()->data($request->all())->success('保存成功！');
    }
}
