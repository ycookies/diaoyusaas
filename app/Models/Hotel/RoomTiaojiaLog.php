<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class RoomTiaojiaLog extends HotelBaseModel
{
	const Batch_tiaojia_type_arr = [
	    '0' => '自宝义',
        '1' => '周末',
        '2' => '春节',
        '3' => '元宵节',
        '4' => '2.14情人节',
        '5' => '51劳动节',
        '6' => '中秋节',
        '7' => '国庆节',
        '8' => '元旦节',
        '9' => '圣诞节',
        '10' => '38妇女节',
    ];
	const Set_price_type = [
	    1 => '加价',
        2 => '减价',
        3 => '上调百分比',
        4 => '下调百分比'
    ];
	const Status_arr = [
	  0 => '未执行',
      1 => '已执行',
    ];
    protected $table = 'room_tiaojia_logs';
    protected $guarded = [];
    protected $appends = ['room_ids_txt','room_sku_ids_txt'];

    public function seller() {
        return $this->hasOne(MerchantUser::class, 'id','seller_id')->select('id','name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name');
    }

    public function room() {
        return $this->hasOne(Room::class, 'id', 'room_id')->select('id','name','logo');
    }

    public function getRoomIdsTxtAttribute(){
        if(empty($this->room_ids)){
            return '';
        }
        $room_list = Room::whereIn('id',json_decode($this->room_ids,true))->select('name')->get();
        $room_name_arr = '';
        if($room_list){
            $room_name_list = array_column($room_list->toArray(),'name');
            $room_name_arr = json_encode($room_name_list,JSON_UNESCAPED_UNICODE);
        }
        return $room_name_arr;
    }

    // 房型销售 sku
    public function getRoomSkuIdsTxtAttribute(){
        if(empty($this->room_sku_ids)){
            return '';
        }
        $room_sku_list = RoomSkuPrice::whereIn('id',json_decode($this->room_sku_ids,true))->select('roomsku_title')->get();
        $room_name_arr = '';
        if(!$room_sku_list->isEmpty()){
            $room_name_list = array_column($room_sku_list->toArray(),'roomsku_title');
            $room_name_arr = json_encode($room_name_list,JSON_UNESCAPED_UNICODE);
        }
        return $room_name_arr;
    }

    // 添加一条日志
    public static function addlog($data){
        $status = self::create($data);
        return $status;
    }


    
}
