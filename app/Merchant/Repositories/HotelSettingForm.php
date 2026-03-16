<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Coupon as Model;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Form;
use App\Models\Hotel\HotelSetting;

class HotelSettingForm extends Repository
{

    protected $api = 'https://api.douban.com/v2/movie/coming_soon';

    // 返回你的id字段名称，默认“id”
    protected $keyName = '_id';

    // 查询编辑页数据
    // 这个方法需要返回一个数组
    public function edit(Form $form)
    {
        $data = HotelSetting::getlists([

        ],'143');
        return $data;
    }
}
