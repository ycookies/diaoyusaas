<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class Assess extends HotelBaseModel
{

    const Star_arr = [
        '1' => '1星',
        '2' => '2星',
        '3' => '3星',
        '4' => '4星',
        '5' => '5星',
    ];
    protected $table = 'assess';
    protected $guarded = [];
    protected $appends = ['img_arr','star_txt'];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }
    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id','name','nick_name','avatar','hotel_id');
    }

    public function room() {
        return $this->hasOne(\App\Models\Hotel\Room::class, 'id', 'room_type');
    }

    public function isJson($string) {
        // 尝试解码字符串
        @json_decode($string);

        // 使用 json_last_error() 检查解码是否成功
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function getImgArrAttribute() {
        if($this->isJson($this->img)){
          return json_decode($this->img,true);
        }
        return [];
    }
    public function getStarTxtAttribute() {
        return !empty(self::Star_arr[$this->score]) ? self::Star_arr[$this->score]:'';
    }

    public static function adds($data){
        $res = self::firstOrCreate(['order_no' =>$data['order_no']],$data);
        return $res;
    }
}
