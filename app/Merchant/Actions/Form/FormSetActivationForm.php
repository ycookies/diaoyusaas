<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Models\Hotel\WxCardCodePre;
use Dcat\Admin\Widgets\Alert;


class FormSetActivationForm extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        $card_id = $this->payload['card_id']; //$request->get('card_id');
        $required_form = !empty($input['required_form']) ? $input['required_form']:[];
        $optional_form = !empty($input['optional_form']) ? $input['optional_form']:[];
        if(empty($required_form)){
            return JsonResponse::make()->error('请填写 填写字段');
        }
        $seller     = Admin::user();
        $wxOpen     = app('wechat.open');
        $hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj     = $wxOpen->hotelWxgzh($hotel_id);
        $settings = [
            'required_form' => [
                'common_field_id_list' => $required_form,
            ]
        ];
        if(!empty($optional_form)){
            $settings['optional_form'] = [
                'common_field_id_list' => $optional_form
            ];
        }
        // 保存配置
        $wx_kaicard_form_field = [];
        $wx_kaicard_form_field['required_form'] = $required_form;
        if(!empty($optional_form)) $wx_kaicard_form_field['optional_form'] = $optional_form;
        $flds     = [
            'wx_kaicard_form_field' => json_encode($wx_kaicard_form_field,JSON_UNESCAPED_UNICODE),
        ];
        \App\Models\Hotel\HotelSetting::createRow($flds,Admin::user()->hotel_id);

        /*$settings = [
            'required_form' => [
                'common_field_id_list' => [
                    'USER_FORM_INFO_FLAG_NAME',
                    'USER_FORM_INFO_FLAG_MOBILE',
                    //'USER_FORM_INFO_FLAG_BIRTHDAY',
                ]
            ],
            'optional_form' => [
                'common_field_id_list' => [
                    'USER_FORM_INFO_FLAG_SEX',
                    'USER_FORM_INFO_FLAG_IDCARD'
                ]
            ],
        ];*/
        $res = $gzhobj->card->member_card->setActivationForm($card_id, $settings);

        if(isset($res['errcode']) && $res['errcode'] == 0){
            return JsonResponse::make()->data([$res])->success('设置成功')->refresh();
        }
        $error_msg = !empty($res['errmsg']) ? $res['errmsg']:'-';
        return JsonResponse::make()->data($res)->error('设置失败:'.$error_msg);
    }

    public function default() {
        $flds     = [
            'wx_kaicard_form_field',
        ];
        $formdata = \App\Models\Hotel\HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $wx_kaicard_form_field = [];
        if(!empty($formdata['wx_kaicard_form_field'])){
            $wx_kaicard_form_field = json_decode($formdata['wx_kaicard_form_field'],true);
        }

        $data = [
            'required_form' => !empty($wx_kaicard_form_field['required_form']) ? $wx_kaicard_form_field['required_form']:['USER_FORM_INFO_FLAG_NAME','USER_FORM_INFO_FLAG_MOBILE'] ,
            'optional_form' => !empty($wx_kaicard_form_field['optional_form']) ? $wx_kaicard_form_field['optional_form']:['USER_FORM_INFO_FLAG_SEX','USER_FORM_INFO_FLAG_IDCARD'] ,
        ];
        return $data;
    }

    public function form() {

        $this->hidden('hotel_id')->value($this->payload['hotel_id']);
        $this->hidden('card_id')->value($this->payload['card_id']);
        //$this->textarea('codes','required_form')->help('');
        $alert = Alert::make('必填信息,选填信息:两者不能选同一个','提示')->info();
        $this->html($alert);
        //$this->html("<ul><li style='font-size: 14px;font-weight: bold'>默认必填字段:</li><li>&nbsp;&nbsp;姓名</li><li>&nbsp;&nbsp;手机号</li><li style='font-size: 14px;font-weight: bold'>默认选填字段:</li><li>&nbsp;&nbsp;姓别</li><li>&nbsp;&nbsp;身份证号</li></ul>");
        $this->checkbox('required_form','必填信息')->options(WxCardCodePre::$userFormInfo)->required();
        $this->checkbox('optional_form','选填信息')->options(WxCardCodePre::$userFormInfo);

        $this->disableResetButton();
        //$this->disableSubmitButton();
    }
}
