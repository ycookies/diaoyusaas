<?php

namespace App\Models\Hotel;
use Illuminate\Database\Eloquent\Model;

class Help extends HotelBaseModel
{
    const Status0 =0;
    const Status1 =1;
    const Status_arr = [
        0 => '关闭',
        1 => '正常',
    ];
    protected $table = 'help';
    public $guarded = [];

    public function type(){
        return $this->belongsTo(HelpType::class,'type_id','id');
    }

    public function getKeywordsAttribute($extra)
    {
        return explode(',',$extra);
        //return array_values(json_decode($extra, true) ?: []);
    }

    public function setKeywordsAttribute($extra)
    {
        $this->attributes['keywords'] = implode(',',array_values($extra));
    }
}
