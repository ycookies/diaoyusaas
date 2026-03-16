<?php

namespace App\Models\Hotel;


class InvoiceRegister extends HotelBaseModel {
    const Status_arr = [
        1 => '审核中',
        2 => '已完成'
    ];
    const Status_arr_label = [
        1 => 'info',
        2 => 'success'
    ];
    const Is_oauth_arr = [
        0 => '未授权',
        1 => '已授权'
    ];
    const Is_oauth_label = [
        0 => 'danger',
        1 => 'success'
    ];
    protected $table = 'invoice_register';
    protected $guarded = [];
}
