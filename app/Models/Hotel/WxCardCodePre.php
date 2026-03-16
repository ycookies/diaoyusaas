<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class WxCardCodePre extends HotelBaseModel
{
	
    protected $table = 'hotel_wx_card_code_pre';
    protected $guarded = [];

    public static $userFormInfo = [
        'USER_FORM_INFO_FLAG_NAME'              => '姓名',
        'USER_FORM_INFO_FLAG_MOBILE'            => '手机号',
        'USER_FORM_INFO_FLAG_SEX'               => '性别',
        'USER_FORM_INFO_FLAG_IDCARD'            => '身份证',
        'USER_FORM_INFO_FLAG_BIRTHDAY'          => '生日',
        'USER_FORM_INFO_FLAG_EMAIL'             => '邮箱',
        'USER_FORM_INFO_FLAG_LOCATION'          => '详细地址',
        'USER_FORM_INFO_FLAG_EDUCATION_BACKGRO' => '教育背景',
        'USER_FORM_INFO_FLAG_INDUSTRY'          => '行业',
        'USER_FORM_INFO_FLAG_INCOME'            => '收入',
        'USER_FORM_INFO_FLAG_HABIT'             => '兴趣爱好'
    ];

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    public function cardtpl() {
        return $this->hasOne(WxCardTpl::class, 'card_id', 'card_id');
    }
    // 增加
    public static function addlog($data){
        return self::create($data);
    }
    
}
