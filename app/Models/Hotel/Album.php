<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class Album extends HotelBaseModel
{
	
    protected $table = 'album';
    protected $guarded = [];
    protected $appends = ['file_path_arr'];

    public function getFilePathArrAttribute()
    {
        if(empty( $this->file_path)){
            return [];
        }
        return json_decode($this->file_path,true);
    }
    public function albumGroup()
    {
        return $this->belongsTo(AlbumGroup::class, 'album_group_id', 'id');
    }
}
