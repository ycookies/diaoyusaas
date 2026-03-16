<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class RoomSheshiConfig extends HotelBaseModel
{
	
    protected $table = 'room_sheshi_config';
    protected $guarded = [];

    protected $appends = ['sheshi_item_arr'];
    public function getSheshiItemArrAttribute() {
        return json_decode($this->sheshi_item,true);
    }

    public static function getSheshiGroup($group_name){
        $sheshi_item = self::where(['sheshi_as'=>$group_name])->value('sheshi_item');
        return $sheshi_item;
    }

    /*public function getSheshiItemAttribute($sheshi_item)
    {
        return json_decode($sheshi_item, true);
    }*/

    
}
