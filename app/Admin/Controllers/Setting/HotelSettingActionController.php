<?php

/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Admin\Controllers\Setting;

use App\Models\Hotel\HotelSetting;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// 列表
class HotelSettingActionController extends Controller
{
    /**
     * @desc 参数存改主入口
     */
    public function formSave(Request $request)
    {
        $action_name = $request->get('action_name');
        $validator   = \Validator::make($request->all(), [
            'action_name' => 'required',
            'hotel_id'    => 'required',
        ], [
            'action_name.required' => '操作项 不能为空',
            'hotel_id.required'    => '酒店ID 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        // 根据操作名不同 调用不用的函数做参数验证，存改
        switch ($action_name) {
            case 'overall':
                return $this->overall($request);
                break;
            case 'profitsharing':
                return $this->profitsharing($request);
                break;
            case 'booking_switch':
                return $this->booking_switch($request);
                break;
            default:
                break;
        }
        return (new WidgetForm())->response()->error('保存时遇到问题，请检查!(没有此项操作[' . $action_name . ']的处理方法)');
    }

    public function overall()
    {
        return JsonResponse::make()->error('暂时不可设置')->refresh();
    }

    // 订房通知对象配置
    public function profitsharing(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'hotel_id'                            => 'required',
            //'trade_fee_author' => 'required',
            //'is_tuangou_profitsharing' => 'required',
            'is_booking_profitsharing' => 'required',
            'profitsharing_author_platform_ratio' => 'required',
            'profitsharing_type'                  => 'required',

            'profitsharing_author_cooperate_ratio_1' => 'required_if:profitsharing_type,1',
            'profitsharing_author_cooperate_ratio_2' => 'required_if:profitsharing_type,2'

            /*'profitsharing_author_platform_price' => 'required_if:profitsharing_type,1',
            'profitsharing_author_agent_price' => 'required_if:profitsharing_type,1',
            'profitsharing_author_businessBD_price' => 'required_if:profitsharing_type,1',
            'profitsharing_author_cooperate_price' => 'required_if:profitsharing_type,1',

            'profitsharing_author_platform_ratio' => 'required_if:profitsharing_type,2',
            'profitsharing_author_agent_ratio' => 'required_if:profitsharing_type,2',
            'profitsharing_author_businessBD_ratio' => 'required_if:profitsharing_type,2',
            'profitsharing_author_cooperate_ratio' => 'required_if:profitsharing_type,2',*/
            //'profitsharing_author' => 'required',
        ], [
            'hotel_id.required'                                  => '酒店ID 不能为空',
            //'is_tuangou_profitsharing'=> '请选择 团购交易是否分账',
            'is_booking_profitsharing.required'       => '请选择 订房交易是否分账 ',
            'profitsharing_author_platform_ratio.required'       => '请填写平台分账比率',
            'profitsharing_type.required'                        => '请选择 合作方 分账方式',
            'profitsharing_author_cooperate_ratio_1.required_if' => '请填写合作方 基于平台分账所得 的分账比率',
            'profitsharing_author_cooperate_ratio_2.required_if' => '请填写合作方 交易金额 的分账比率',
            /*'trade_fee_author.required' => '请选择 交易手续费出资方',

            'profitsharing_author_platform_price.required_if' => '请选择分账 分账方式',
            'profitsharing_author_agent_price.required_if' => '请选择分账 分账方式',
            'profitsharing_author_businessBD_price.required_if' => '请选择分账 分账方式',
            'profitsharing_author_cooperate_price.required_if' => '请选择分账 分账方式',

            'profitsharing_author_platform_ratio.required_if' => '请选择分账 分账方式',
            'profitsharing_author_agent_ratio.required_if' => '请选择分账 分账方式',
            'profitsharing_author_businessBD_ratio.required_if' => '请选择分账 分账方式',
            'profitsharing_author_cooperate_ratio.required_if' => '请选择分账 分账方式',

            'profitsharing_author.required' => '请选择分账 分账参于者',*/

        ]);

        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->first());
        }
        $hotel_id = $request->get('hotel_id');
        $insdata  = [
            'is_tuangou_profitsharing'=> 0,
            'is_booking_profitsharing' => $request->is_booking_profitsharing,
            'profitsharing_author_platform_ratio' => $request->profitsharing_author_platform_ratio,
            'profitsharing_type'                  => $request->profitsharing_type,
        ];
        if (!empty($request->profitsharing_author)) {
            $profitsharing_author            = array_filter($request->profitsharing_author);
            $insdata['profitsharing_author'] = json_encode($profitsharing_author, JSON_UNESCAPED_UNICODE);
        }
        // 按固定金额分账
        if ($request->profitsharing_type == '1') {
            $price_arr = [
                'profitsharing_author_cooperate_ratio_1' => $request->profitsharing_author_cooperate_ratio_1,
                /*'profitsharing_author_agent_price' => $request->profitsharing_author_agent_price,
                'profitsharing_author_businessBD_price' => $request->profitsharing_author_businessBD_price,
                'profitsharing_author_cooperate_price' => $request->profitsharing_author_cooperate_price*/
            ];
            // 删掉另一种
            $field_arr = ['profitsharing_author_cooperate_ratio_2'];
            HotelSetting::delsRow($field_arr, $hotel_id);
            $insdata = array_merge($insdata, $price_arr);
        }
        //  按百份比分账
        if ($request->profitsharing_type == '2') {
            $price_arr = [
                'profitsharing_author_cooperate_ratio_2' => $request->profitsharing_author_cooperate_ratio_2,
                /*'profitsharing_author_agent_ratio' => $request->profitsharing_author_agent_ratio,
                'profitsharing_author_businessBD_ratio' => $request->profitsharing_author_businessBD_ratio,
                'profitsharing_author_cooperate_ratio' => $request->profitsharing_author_cooperate_ratio,*/
            ];
            // 删掉另一种
            $field_arr = ['profitsharing_author_cooperate_ratio_1'];
            HotelSetting::delsRow($field_arr, $hotel_id);
            $insdata = array_merge($insdata, $price_arr);
        }
        $sts = HotelSetting::createRow($insdata, $hotel_id, 'profitsharing');

        // 添加服务商到酒店分账接受方
        $insdata = [
            'hotel_id'      => $hotel_id,
            'relation_type' => 'SERVICE_PROVIDER',
            'type'          => 'MERCHANT_ID',
            'receiver_uid'  => '1',
            'account'       => '1566291601',
            'name'          => '深圳市融宝科技有限公司',
            'rate'          => $request->profitsharing_author_platform_ratio,
        ];
        \App\Models\Hotel\ProfitsharingReceiver::add($insdata);

        return JsonResponse::make()->data($request->all())->success('成功！')->refresh();
    }

    public function booking_switch(Request $request)
    {
    }
}
