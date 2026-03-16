<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

use Dcat\Admin\Traits\ModelTree;
class UserCenterNav extends HotelBaseModel
{
    use ModelTree;
    protected $table = 'user_center_nav';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }
    
}
