<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class OftenLvkeinfo extends HotelBaseModel
{
	
    protected $table = 'often_lvkeinfo';
    protected $guarded = [];
    protected $appends = ['is_benren_txt'];

    public function getIsBenrenTxtAttribute(){
        return $this->is_benren == 1 ? "默认":'';
    }

    public static function createOrUpdate($where,$update){
        return self::updateOrInsert($where,$update);
    }
    
}
