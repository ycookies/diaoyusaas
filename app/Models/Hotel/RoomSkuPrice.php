<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class RoomSkuPrice extends HotelBaseModel
{

    protected $table = 'room_sku_price';
    protected $guarded = [];
    protected $appends = ['roomsku_where_str','roomsku_gift_str','roomsku_tags_str','roomsku_zaocan_str','roomsku_zaocan_title'];

    public function setRoomskuTagsAttribute($value){
        if (is_array($value)) {
            $this->attributes['roomsku_tags'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }else{
            $this->attributes['roomsku_tags'] = $value;
        }
    }

    public function setRoomskuFuwuAttribute($value){
        if (is_array($value)) {
            $this->attributes['roomsku_fuwu'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }else{
            $this->attributes['roomsku_fuwu'] = $value;
        }
    }

    public function getRoomskuPriceAttribute($value){
        return $this->formatNumber($value);
    }

    // 处理数值
    public function formatNumber($number) {
        if ($number == round($number)) {
            $new_number =  number_format($number, 0); // 显示整数部分
        } else {
            $new_number =  number_format($number, 2); // 显示小数部分，最多保留两位小数
        }

        return str_replace(',','',$new_number);
    }

    public function setRoomskuGiveCouponAttribute($value){
        if (is_array($value)) {
            $this->attributes['roomsku_give_coupon'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }else{
            $this->attributes['roomsku_give_coupon'] = $value;
        }
    }
    public function setRoomskuGiftAttribute($value){
        if (is_array($value)) {
            $this->attributes['roomsku_gift'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }else{
            $this->attributes['roomsku_gift'] = $value;
        }
    }

    // 早餐转换成字符串 描述
    public function getRoomskuZaocanStrAttribute(){
        if(empty($this->roomsku_zaocan)){
            return '无餐食';
        }
        return '早餐 '.$this->roomsku_zaocan.'份/天';
    }

    // 早餐转换成字符串 标题
    public function getRoomskuZaocanTitleAttribute(){
        if(empty($this->roomsku_zaocan)){
            return '无餐食';
        }
        return $this->roomsku_zaocan.'份早餐';
    }

    public function getRoomskuWhereStrAttribute(){
        $values = '';
        if(!empty($this->roomsku_where)){
            $where_arr =  json_decode($this->roomsku_where, true);
            if(!empty($where_arr['attrs'])){
                $values_arr = [];
                foreach ($where_arr['attrs'] as $key => $items){
                    $values_arr[] = $key.':'.$items[0];
                }
                if(!empty($values_arr)){
                    $values = implode(',',$values_arr);
                }
            }
        }
        return $values;
    }

    public function getRoomskuTagsAttribute($value){
        if(!empty($value)){
            return json_decode($value, true);
        }
        return '';
    }

    // 增加 roomstr_tags_str 字段
    public function getRoomskuTagsStrAttribute(){
        if(!empty($this->roomsku_tags)){
            $roomsku_tags = $this->roomsku_tags;
            if(!is_array($this->roomsku_tags)){
                $roomsku_tags = json_decode($this->roomsku_tags);
            }
            $sku_tags_name =  RoomSkuTag::whereIn('id', $roomsku_tags)->pluck('sku_tags_name')->toArray();

            return json_encode($sku_tags_name);
        }
        return '';
    }

    // 增加 roomsku_gift_str 字段
    public function getRoomskuGiftStrAttribute(){
        if(!empty($this->roomsku_gift)){
            $roomsku_gift = $this->roomsku_gift;
            if(!is_array($this->roomsku_gift)){
                $roomsku_gift = json_decode($this->roomsku_gift);
            }
            $sku_gift_name =  RoomSkuGift::whereIn('id', $roomsku_gift)->pluck('sku_gift_name')->toArray();

            return json_encode($sku_gift_name);
        }
        return '';
    }
    public function getRoomskuGiveCouponAttribute($value){
        if(!empty($value)){
            return json_decode($value, true);
        }
        return '';
    }
    public function getRoomskuGiftAttribute($value){
        if(!empty($value)){
            return json_decode($value, true);
        }
        return '';
    }

    public function room() {
        return $this->hasOne(\App\Models\Hotel\Room::class, 'id', 'room_id')
            ->select('id','name','hotel_id','name_as','logo','area','bed_num','people');
    }

    // 获取当日剩余房量
    public static function getRoomSkuNum($model,$days_time){
        $total_num = $model->roomsku_stock;
        $booking_num = RoomBookingLog::where(['room_sku_id'=>$model->id,'date_time'=>$days_time])->count();
        return bcsub($total_num,$booking_num,0);
    }

    /**
     * @desc 获取客房预定日期范围内 每天是否都有房量
     * @param $room_id 房间ID
     * @param $arrival_time 入驻时间
     * @param $departure_time 离店时间
     * @return array
     * author eRic
     * dateTime 2024-06-09 00:04
     */
    public static function getBookingSkuRangeIsFM($room_sku_id,$arrival_time,$departure_time){
        $info = RoomSkuPrice::where(['id'=> $room_sku_id])->first();
        $departure_time = date('Y-m-d',strtotime($departure_time." -1 day")); // 去掉离店当天
        $booking_date_range = getDatesInRange($arrival_time,$departure_time);
        $booking_date_range_list =[];
        $tips = '';
        foreach ($booking_date_range as $key => $datetime) {
            $num = RoomSkuPrice::getRoomSkuNum($info,$datetime);
            $booking_date_range_list[$datetime] = $num;
            if(empty($num)){
                $tips = $datetime.':已满房 无法预订';
                break;
            }
        }
        if(!empty($tips)){
            return $tips;
        }
        return true;
    }

    /**
     * @desc 检查某一天 是否满房
     * @param $room_id 房间ID
     * @param $arrival_time 入驻时间
     * @param $departure_time 离店时间
     * @return array
     * author eRic
     * dateTime 2024-06-09 00:04
     */
    public static function getBookingSkuDateIsFM($room_sku_id,$datetime){
        $info = self::where(['id'=> $room_sku_id])->first();
        $num = self::getRoomSkuNum($info,$datetime);
        $tips = '';
        if(empty($num)){
            $tips = '已满房';
        }
        if(!empty($tips)){
            return $tips;
        }
        return true;
    }

    /**
     * @desc 更新小程序码
     * @param $id
     * @param $imgurl
     * author eRic
     * dateTime 2025-03-17 21:42
     */
    public static function upQrcode($id,$imgurl){
        return self::where(['id'=> $id])->update(['roomsku_qrcode'=>$imgurl]);
    }
}
