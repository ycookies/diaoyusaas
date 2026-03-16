<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class TicketsVerificationRecord extends HotelBaseModel
{
	
    protected $table = 'tickets_verification_records';

    protected $guarded = [];

    // 核销渠道
    const Verifiy_type_arr = [
        1 => 'POS机',
        2 => 'PC后台',
        3 => '小程序',
    ];
    // 核销状态
    const Status_arr = [
        0 => '未核销',
        1 => '已核销',
    ];
    //protected $fillable = ['id','hotel_id','ticket_id','verifier_id','verified_at','device_info'];
    public function ticketsCode() {
        return $this->hasOne(TicketsCode::class, 'id', 'ticket_id');
    }

    public function user() {
        return $this->hasOne(MerchantUser::class, 'id', 'verifier_id');
    }

}
