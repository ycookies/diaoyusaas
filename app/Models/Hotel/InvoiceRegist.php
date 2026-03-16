<?php

namespace App\Models\Hotel;


class InvoiceRegist extends HotelBaseModel {

    protected $table = 'invoice_regists';

    // 1审核中,2后台审核,3通过票宝通审核
    public static $status_arr = [
        1 => '审核中',
        2 => '后台审核',
        3 => '通过票宝通审核'
    ];
}
