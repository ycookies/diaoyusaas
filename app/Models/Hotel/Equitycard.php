<?php

namespace App\Models\Hotel;


class Equitycard extends HotelBaseModel {

    protected $table = 'equitycard';
    // 1季卡2半年卡3年卡
    public static $attribute_arr = [
        1 => '月卡',
        2 => '季卡',
        3 => '半年卡',
        4 => '年卡',
    ];

}
