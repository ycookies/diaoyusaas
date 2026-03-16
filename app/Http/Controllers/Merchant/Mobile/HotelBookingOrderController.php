<?php

namespace App\Http\Controllers\Merchant\Mobile;

use Illuminate\Http\Request;
use App\Models\Cgcms\Archive;
use App\Models\Cgcms\Arctype;
use App\Models\Cgcms\Ad;
use App\Models\Cgcms\Partner;


class HotelBookingOrderController extends BaseController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewBookingOrder(Request $Request)
    {

        return view('style1.pc.index','');
    }
}
