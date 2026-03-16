<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;


class Apilog extends HotelBaseModel
{
    //
    //public $primaryKey = 'goods_id';
    protected $table = 'apilogs';
    public $guarded= [];

    // 访问器
    public function getRequestTxtAttribute(){
        if(is_json($this->request)){
            return '<pre>'.print_r(json_decode($this->request,true)).'</pre>';
        }else{
            return '<pre>'.print_r(unserialize($this->request)).'</pre>';
        }

    }
    public function getResultTxtAttribute(){
        if(is_json($this->result)){
            return '<pre>'.print_r(json_decode($this->result,true)).'</pre>';
        }else{
            return '<pre>'.print_r(unserialize($this->result)).'</pre>';
        }
    }
    public function users() {
        return $this->belongsTo(\App\Admin::class, 'user_id');
    }

    public function is_json($str){
        return is_null(json_decode($str));
    }
}
