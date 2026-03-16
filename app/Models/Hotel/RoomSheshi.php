<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class RoomSheshi extends HotelBaseModel
{
	
    protected $table = 'room_sheshi';
    protected $guarded = [];

    protected $appends = ['sheshi_item_arr'];
    public function getSheshiItemArrAttribute() {
        return json_decode($this->sheshi_item,true);
    }

    public static function getSheshiGroup($hotel_id,$group_name){
        $sheshi_item = self::where(['hotel_id'=>$hotel_id,'sheshi_as'=>$group_name])->value('sheshi_item');
        return $sheshi_item;
    }

    public static function setSheshiGroup($hotel_id, $room_id ,$group_name,$group_value){
        //$sheshi_item = self::where(['hotel_id'=>$hotel_id,'sheshi_as'=>$group_name])->update(['sheshi_item'=>$group_value]);
        $where = ['hotel_id'=>$hotel_id,'room_id'=>$room_id,'sheshi_as'=>$group_name];
        $update = ['sheshi_item'=> $group_value];
        $sheshi_res = self::updateOrCreate($where,$update);
        return $sheshi_res;

    }

    /*public function getSheshiItemAttribute($sheshi_item)
    {
        return json_decode($sheshi_item, true);
    }*/

    
}
