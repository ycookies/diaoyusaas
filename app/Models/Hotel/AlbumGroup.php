<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class AlbumGroup extends HotelBaseModel
{
	
    protected $table = 'album_groups';
    protected $guarded = [];

    public function album(){
        return $this->hasMany(\App\Models\Hotel\Album::class,'album_group_id','id')->where(['status'=>1])->select('id','album_group_id','title','description','file_path');
    }
}
