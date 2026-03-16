<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class RuzhuInfo extends HotelBaseModel
{
    const  Status_0 = 0;
    const  Status_1 = 1;
    const  Status_2 = 2;
    const  Status_3 = 3;

    const Status_arr = [
        0 => '待填写',
        1 => '资料已提交',
        2 => '进件中',
        3 => '进件完成',
    ];
    protected $table = 'hotel_ruzhu_info';
    protected $guarded = [];


    /*public function getIndoorPicAttribute($extra)
    {
        return explode(',',$extra);
        //return array_values(json_decode($extra, true) ?: []);
    }

    public function setIndoorPicAttribute($extra)
    {
        $this->attributes['indoor_pic'] = implode(',',array_values($extra));
    }*/


}
