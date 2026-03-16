<?php

namespace App\Models\Hotel\Goods;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;
use Dcat\Admin\Traits\ModelTree;

class GoodsCat extends HotelBaseModel
{
    use SoftDeletes;
    use ModelTree; // 必须要加上这一项

    protected $table = 'goods_cats';
    protected $guarded = [];

    // 定义 title 对应的字段
    protected $titleColumn = 'name';

}
