<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WxCardTpl extends HotelBaseModel
{
    use SoftDeletes;
    const Demotpl  = [
        'background_pic_url' => 'http://mmbiz.qpic.cn/sz_mmbiz_png/Bq2nys5Z4bZZGcWxhr3A3IhTL9Ch1jeictTlWPVByP02E7GvIQWtEJHWHEwOuTTQxYEeMoTxOjIrNUJqt35etVA/0?wx_fmt=png', // 商家自定义会员卡背景图 1000*600
        'base_info'          => [
            "logo_url"                 => "http://mmbiz.qpic.cn/sz_mmbiz_png/Bq2nys5Z4bZZGcWxhr3A3IhTL9Ch1jeicEElWUoaTufKMB26R9oM820uqCJMHL90tNhQicjibwtkBlmRPcic73qu2g/0?wx_fmt=png", //卡券的商户logo，建议像素为300*300。
            'brand_name'               => '融宝易住', // 商户名字,字数上限为12个汉字
            'code_type'                => 'CODE_TYPE_TEXT', // Code展示类型 "CODE_TYPE_TEXT" 文本 "CODE_TYPE_BARCODE" 一维码 "CODE_TYPE_QRCODE" 二维码 "CODE_TYPE_ONLY_QRCODE" 仅显示二维码 "CODE_TYPE_ONLY_BARCODE" 仅显示一维码 "CODE_TYPE_NONE" 不显示任何码型
            'title'                    => '悦享会员卡', // 卡券名，字数上限为9个汉字
            "color"                    => "Color010", // 券颜色
            "notice"                   => "使用时向服务员出示此券", //卡券使用提醒，字数上限为16个汉字。
            "service_phone"            => "13725589225", // 客服电话
            "description"              => "不可与其他优惠同享", // 卡券使用说明，字数上限为1024个汉字。
            "date_info"                => [
                "type" => "DATE_TYPE_PERMANENT", // 永久有效
                //"type" => "DATE_TYPE_FIX_TIME_RANGE", //有效日期
                //"type" => "DATE_TYPE_FIX_TERM" // 有效天数
            ],
            "sku"                      => [
                "quantity" => 90000000 // 库存
            ],
            "get_limit"                => 1, // 每人可领券的数量限制，建议会员卡每人限领一张
            "use_custom_code"          => true, // 是否自定义Code码。填写true或false，默认为false 通常自有优惠码系统的开发者选择自定义Code码，详情见 是否自定义code
            "can_give_friend"          => false, //卡券是否可转赠,默认为true
            "custom_url_name"          => "立即使用", // 自定义跳转外链的入口名字
            "custom_url"               => "http://weixin.qq.com", // 自定义跳转的URL
            "custom_url_sub_title"     => "6个汉字tips", // 显示在入口右侧的提示语。
            "promotion_url_name"       => "营销入口1", // 营销场景的自定义入口名称
            "promotion_url"            => "http://www.qq.com", // 入口跳转外链的地址链接
            //"promotion_ url_sub_title" => '右侧的提示语',// 显示在营销入口右侧的提示语
            "need_push_on_view"        => true // 填写true为用户点击进入会员卡时推送事件，默认为false。详情见 进入会员卡事件推送
        ],
        "supply_bonus"       => false, //显示积分，填写true或false，如填写true，积分相关字段均为必 填 若设置为true则后续不可以被关闭。
        "supply_balance"     => false, // 是否支持储值，填写true或false。如填写true，储值相关字段均为必 填 若设置为true则后续不可以被关闭。该字段须开通储值功能后方可使用， 详情见： 获取特殊权限
        "prerogative"        => "会员卡特权说明,限制1024汉字", // 会员卡特权说明,限制1024汉字
        "auto_activate"      => true, // 设置为true时用户领取会员卡后系统自动将其激活，无需调用激活接口，详情见 自动激活
        "custom_field1"      => [ // 自定义会员信息类目，会员卡激活后显示,包含name_type (name) 和url字段
                                  "name_type" => "FIELD_NAME_TYPE_LEVEL", // 会员信息类目半自定义名称
                                  "name" => "", // 会员信息类目自定义名称
                                  "url"       => "http://www.qq.com" // 点击类目跳转外链url
        ],
        "activate_url"       => "http://www.qq.com", // 激活会员卡的url
        "custom_cell1"       => [ // 自定义会员信息类目，会员卡激活后显示。
                                  "name" => "使用入口2", // 入口名称。
                                  "tips" => "激活后显示", // 入口右侧提示语，6个汉字内。
                                  "url"  => "http://www.qq.com" // 入口跳转链接。
        ],
        'discount'           => '9' // 折扣

    ];
	const Color_arr = [
	   'Color010' => 'Color010',
       'Color020' => 'Color020',
       'Color030' => 'Color030',
       'Color040' => 'Color040',
       'Color050' => 'Color050',
       'Color060' => 'Color060',
       'Color070' => 'Color070',
       'Color080' => 'Color080',
       'Color081' => 'Color081',
       'Color082' => 'Color082',
       'Color090' => 'Color090',
       'Color100' => 'Color100',
       'Color101' => 'Color101',
       'Color102' => 'Color102',
    ];
	const Status_arr = [
	    1 => '待创建',
        2 => '已创建',
        3 => '已审核',
    ];
    protected $table = 'hotel_wx_card_tpl';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }
    
}
