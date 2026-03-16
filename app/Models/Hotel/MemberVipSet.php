<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MemberVipSet extends HotelBaseModel
{
    const Level1 = '等级1';
    const Level2 = '等级2';
    const Level3 = '等级3';
    const Level4 = '等级4';
    const Level5 = '等级5';
    const LevelArr = [
        1 => '等级1',
        2 => '等级2',
        3 => '等级3',
        4 => '等级4',
        5 => '等级5',
    ];
    const Unit_arr = [
        '月' => '每月',
        '半年' => '每半年',
        '年' => '每年'
    ];
    protected $table = 'member_vip_set';
    protected $guarded = [];

    public function right(){
        return $this->hasMany(MemberRight::class, 'member_id');
    }
    
}
