<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class Seller extends HotelBaseModel {

    protected $table = 'hotel';
    //public $timestamps = false;
    public $guarded = [];

    protected $appends = ['early_arrival_time','late_arrival_time','early_departure_time','late_departure_time','hotel_img','star_txt']; //

    // 不可取消
    public static $non_cancelling_time_arr = [
        '08' => '当日08:00以后',
        '10' => '当日10:00以后',
        '12' => '当日12:00以后',
        '14' => '当日14:00以后',
        '16' => '当日16:00以后',
        '18' => '当日18:00以后',
        '20' => '当日20:00以后',
        '22' => '当日22:00以后',
        '24' => '当日24:00以后',
        '26' => '次日02:00以后',
        '28' => '次日04:00以后',
        '30' => '次日06:00以后',
    ];
    // 是否 支持第三方支付
    public static $otherpay_support_arr = [
        2 => '支持第三方支付',
        1 => '不支持第三方支付',

    ];
    // 酒店类型
    public static $store_type_arr = [
        1 => '酒店行业',
        2 => '民宿行业',
        3 => '农家乐',
        4 => '精品茶室',
    ];
    // 酒店类型
    public static $hotel_star_arr = [
        '2' => '暂无星级(经济型)',
        '3' => '三星级',
        '4' => '四星级',
        '5' => '五星级',
    ];
    // 第三方支付 支持
    public static $otherpay_type_arr = [
        1 => '支付宝支付',
        2 => '微信支付',
        3 => '云闪付',
        4 => '华为支付',
    ];
    // 刷卡
    public static $card_support_arr = [
        2 => '可刷卡',
        1 => '不可刷卡',
    ];
    // 可刷卡支持
    public static $card_type_arr = [
        1 => 'Master',
        2 => 'Visa',
        3 => 'Amex',
        4 => '大来（Diners Club）',
        5 => 'Jcb',
        6 => '中国银联（China UnionPay）',
    ];
    // 宠物政策
    public static $pet_arr = [
        1 => '不可携带宠物',
        2 => '允许携带宠物，不收取额外费用',
        3 => '允许携带宠物，收取额外费用',
        4 => '可携带宠物',
    ];
    public static $hotel_facility_arr = [
        'wifi','停车','早餐','健身房','会议室','行李寄存','接机','游泳池',
        '接站服务', '行李寄存', '代客泊车', '租车服务', '24小时前台服务', '旅游票务服务', '邮政服务', '叫醒服务', '送餐服务', '外送洗衣服务', '信用卡结算服务', '商务中心', '商务服务', '会议设施', '前台保险柜', '电梯', '大堂报纸', '公共音响系统', '多媒体演示系统', '公共区域监控系统', '大堂吧', '中餐厅', '公用吹风机'
        ];
    //
    public static $service_facility_arr = [
        '接站服务', '行李寄存', '代客泊车', '租车服务', '24小时前台服务', '旅游票务服务', '邮政服务', '叫醒服务', '送餐服务', '外送洗衣服务', '信用卡结算服务', '商务中心', '商务服务', '会议设施', '前台保险柜', '电梯', '大堂报纸', '公共音响系统', '多媒体演示系统', '公共区域监控系统', '大堂吧', '中餐厅', '公用吹风机',
    ];

    public function getEarlyArrivalTimeAttribute() {
        if(!empty($this->arrival_departure_time)){
            $mk = explode('-',$this->arrival_departure_time);
            return $mk[0];
        }
        return '';
    }
    public function getLateArrivalTimeAttribute() {
        if(!empty($this->arrival_departure_time)){
            $mk = explode('-',$this->arrival_departure_time);
            return $mk[1];
        }
        return '';
    }
    // late_arrival_time
    public function getEarlyDepartureTimeAttribute() {
        if(!empty($this->arrival_departure_time)){
            $mk = explode('-',$this->arrival_departure_time);
            return $mk[2];
        }
        return '';
    }
    public function getLateDepartureTimeAttribute() {
        if(!empty($this->arrival_departure_time)){
            $mk = explode('-',$this->arrival_departure_time);
            return $mk[3];
        }
        return '';
    }

    public function getStarTxtAttribute() {
        return !empty(self::$hotel_star_arr[$this->star]) ? self::$hotel_star_arr[$this->star]:'';
    }

    public function setCardTypeAttribute($extra) {

        $this->attributes['card_type'] = implode(',', array_values($extra));
    }

    public function setOtherpayTypeAttribute($extra) {

        $this->attributes['otherpay_type'] = implode(',', array_values($extra));
    }

    /*public function setServiceFacilityAttribute($extra) {

        $this->attributes['service_facility'] = implode(',', array_values($extra));
    }*/

    public function setWakeAttribute($extra) {

        $this->attributes['wake'] = implode(',', array_values($extra));
    }
    public function getHotelImgAttribute() {
        $mk = explode(',',$this->img);
        return $mk;
    }

    // 房间

    /**
     * @desc
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * author eRic
     * dateTime 2023-08-05 11:10          "retail_price": "288.00",
    "agreement_price": "288.00",
    "member_price": "288.00",
     */

    public function user() {
        return $this->hasOne(MerchantUser::class, 'id', 'hotel_user_id');
    }

    public function roomFull(){
        return $this->hasMany(Room::class, 'hotel_id', 'id')
            ->select('id','hotel_id','name','logo','price','moreimg','retail_price','agreement_price','member_price')->orderBy('recommend','DESC');
    }

    public function room(){
        return $this->hasMany(Room::class, 'hotel_id', 'id')
            ->select('id','hotel_id','name','logo','price','moreimg','retail_price','agreement_price','member_price')->where(['state'=>1])->orderBy('recommend','DESC');
    }

    public function facilitys(){
        return $this->hasMany(OffsiteFacility::class, 'hotel_id', 'id')
            ->select('id','hotel_id','name','icon','description')->where(['is_show'=>1,'is_recommend'=> 1])->orderBy('sorts','DESC');
    }
}
