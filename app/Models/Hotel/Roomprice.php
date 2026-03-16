<?php

namespace App\Models\Hotel;

use Carbon\Carbon;

class Roomprice extends HotelBaseModel {

    protected $table = 'room_price';
    protected $guarded = [];
    //public $timestamps = false;


    /*public $appends = ['main_img','moreimg_map'];


    // 获取房间主图
    public function getMainImgAttribute(){
       $picimg =  explode(',',$this->attributes['moreimg']);
       if(!empty($picimg)){
           return $picimg[0];
       }
        return '';
    }
    // 获取房间主图
    public function getMoreimgMapAttribute(){
        $picimg =  explode(',',$this->attributes['moreimg']);
        return $picimg;
    }*/
    public function setMoreimgAttribute($image) {
        if (is_array($image)) {
            $this->attributes['moreimg'] = json_encode($image);
        }
    }

    public function getMoreimgAttribute($image) {
        return json_decode($image, true);
    }

    public static function showDaysPrice($days, $room_id) {
        $htmls = '<div class="text text-muted">';
        $htmls .= "<a class='price_set' href='#'  data-type = 'online_price' data-type-name = '线上价' data-days='$days' data-room_id='$room_id' data-price='388.00'>线上价:388.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'Offline_price' data-type-name = '门市价' data-days='$days' data-room_id='$room_id' data-price='400.00'>门市价:400.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'xieyi_price' data-type-name = '协议价' data-days='$days' data-room_id='$room_id' data-price='344.00'>协议价:344.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'vip_price' data-type-name = '会员价' data-days='$days' data-room_id='$room_id' data-price='388.00'>会员价:388.00</a><br>";
        $htmls .= '</div>';
        return $htmls;
    }

    public static function calendar($year, $month, $days = '') {
        //获取年
        //$year = $request->input('year', now()->year);
        //获取月份
        //$month     = $request->input('month', now()->month);
        if (empty($days)) {
            $days = date('d');
        }
        $yearMonth     = sprintf("%d-%s", $year, $month);
        $yearMonthDays = $yearMonth . '-' . $days;
        //获取月份第一天所在的星期
        $firstDayOfWeek = Carbon::parse($yearMonthDays)->dayOfWeek;

        //补全
        $day      = 0;
        $calendar = [];
        for ($i = 0; $i < 6; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($firstDayOfWeek != 0 and $i == 0) {
                    //根据月初第一天所在的星期，计算出之前几天的日子
                    $day  = Carbon::parse($yearMonthDays)->subDays($firstDayOfWeek - $j)->day;
                    $date = Carbon::parse($yearMonthDays)->subDays($firstDayOfWeek - $j)->format("Y-m-d");
                } else {
                    $day++;
                    $date = Carbon::parse($yearMonthDays)->addDays($day - 1)->format("Y-m-d");
                }
                $calendar[$i][] = $date;

            }
        }
        return $calendar;
    }

    public static function calendars($year, $month) {
        //获取年
        //$year = now()->year;
        //获取月份
        //$month     = now()->month;
        $yearMonth = sprintf("%d-%s", $year, $month);
        //获取月份第一天所在的星期
        $firstDayOfWeek = Carbon::parse("$yearMonth-01")->dayOfWeek;

        //补全
        $day      = 0;
        $calendar = [];
        for ($i = 0; $i < 6; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($firstDayOfWeek != 0 and $i == 0) {
                    //根据月初第一天所在的星期，计算出之前几天的日子
                    $day  = Carbon::parse("$yearMonth-01")->subDays($firstDayOfWeek - $j)->day;
                    $date = Carbon::parse("$yearMonth-01")->subDays($firstDayOfWeek - $j)->format("Y-m-d");
                } else {
                    $day++;
                    $date = Carbon::parse("$yearMonth-01")->addDays($day - 1)->format("Y-m-d");
                }
                $calendar[$i][] = $date;
            }
        }

        return $calendar;
    }

    /**
     * @desc  设置房间默认价格
     * @param $room_id
     * @param $room_old_price 旧价格
     * @param $room_new_price 新价格
     * author eRic
     * dateTime 2024-04-09 18:37
     */
    public static function setRoomDefaultPrice($room_id, $room_old_price, $room_new_price) {
        $where = [
            ['room_id', '=', $room_id],
            ['calendar_date', '>=', Carbon::now()->toDateString()],
            ['mprice', '=', $room_old_price]
        ];
        Roomprice::where($where)->update(['mprice' => $room_new_price]);
        self::getRoomDaysPricelist($room_id, 0);
    }

    // 获取客房单个日历价格
    public static function getRoomDaysPrice($room_id, $daydate) {
        $parm      = self::getRoomDaysPricelist($room_id, 0);
        $new_price = [];
        foreach ($parm as $key => $itmes) {
            if ($itmes['calendar_date'] == $daydate) {
                $new_price = $itmes;
                break;
            }
        }
        return $new_price;
    }

    // 获取客型sku单个日历价格
    public static function getRoomSkuDaysPrice($room_sku_id, $daydate) {
        $parm      = self::getRoomSkuDaysPricelist($room_sku_id, 0);
        $new_price = [];
        foreach ($parm as $key => $itmes) {
            if ($itmes['calendar_date'] == $daydate) {
                $new_price = $itmes;
                break;
            }
        }
        return $new_price;
    }

    // 获取客房日期范围总价格
    public static function getRoomDateRangePrice($room_id, $start_time, $end_time) {

        $new_end_time = date('Y-m-d', strtotime($end_time . ' -1 day')); // 减去离店日期
        $days_num     = two_time_diff_days($end_time, $start_time);
        $where        = [
            ['room_id', '=', $room_id],
            ['calendar_date', '>=', $start_time],
            ['calendar_date', '<=', $new_end_time]
        ];
        $where1       = [
            ['room_id', '=', $room_id],
            ['calendar_date', '>=', $start_time],
            ['calendar_date', '<=', $new_end_time],
            ['open_status', '=', 0],
        ];
        // 检查这个日期范围内是不是有不可订的 返回满房
        $status = Roomprice::where($where1)->count();
        if (!empty($status)) {
            return false;
        }
        // 房间已经被关闭 返回满房
        $roominfo = Room::where(['id' => $room_id])->first();
        if (!$roominfo || empty($roominfo->state)) {
            return false;
        }

        $total_mprice = Roomprice::where($where)->sum('mprice');
        if (!empty($total_mprice)) {
            return $total_mprice;
        }
        $total_danprice = bcmul($roominfo->price, $days_num, 2);//
        return $total_danprice;
    }

    // 获取房型销售SKU日期范围总价格
    public static function getRoomSkuDateRangePrice($room_sku_id, $start_time, $end_time) {

        $new_end_time = date('Y-m-d', strtotime($end_time . ' -1 day')); // 减去离店日期
        $days_num     = two_time_diff_days($end_time, $start_time);
        $where        = [
            ['room_sku_id', '=', $room_sku_id],
            ['calendar_date', '>=', $start_time],
            ['calendar_date', '<=', $new_end_time]
        ];
        $where1       = [
            ['room_sku_id', '=', $room_sku_id],
            ['calendar_date', '>=', $start_time],
            ['calendar_date', '<=', $new_end_time],
            ['open_status', '=', 0],
        ];
        // 检查这个日期范围内是不是有不可订的 返回满房
        $status = Roomprice::where($where1)->count();
        if (!empty($status)) {
            return false;
        }
        // 房间已经被关闭 返回满房
        $roominfo = RoomSkuPrice::where(['id' => $room_sku_id])->first();
        /*if (empty($roominfo->is_full_room)) {
            return false;
        }*/

        $total_mprice = Roomprice::where($where)->sum('mprice');
        if (!empty($total_mprice)) {
            return $total_mprice;
        }
        $total_danprice = bcmul($roominfo->roomsku_price, $days_num, 2);//
        return $total_danprice;
    }

    /**
     * @desc 获取客房日历价格列表 无限制
     * @param $room_id
     * @param int $is_cache
     * @param bool $days_num
     * @return array|mixed
     * author eRic
     * dateTime 2024-04-09 21:15
     */
    public static function getRoomCalendarPrice($hotel_id, $room_id, $is_cache = 1, $days_num = 90) {
        $info          = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();
        $where         = [
            ['hotel_id', '=', $hotel_id],
            ['room_id', '=', $room_id],
            ['calendar_date', '>=', Carbon::now()->toDateString()],
            ['calendar_date', '<=', date('Y-m-d', strtotime('+' . $days_num . ' day'))]
        ];
        $parm          = Roomprice::where($where)->select('room_id', 'calendar_date', 'mprice', 'open_status', 'booking_status')->get();
        $new_Roomprice = []; // 存放价格表信息
        if ($parm) {
            foreach ($parm as $items) {
                // 使用日期做为键名
                $new_Roomprice[$items['calendar_date']] = collect($items)->toArray();
            }
        }
        $calendar_arr = [];
        $currentDate  = date('Y-m-d'); // 获取当前日期
        for ($i = 0; $i <= $days_num; $i++) {
            $daydate = date('Y-m-d', strtotime($currentDate . ' +' . $i . ' day')); // 加一天
            $pinfo   = [
                'id'             => $i,
                'room_id'        => $room_id,
                'calendar_date'  => $daydate,
                'mprice'         => formatFloats($info->price),
                'total_num'      => $info->total_num,
                'open_status'    => 1,
                'booking_status' => 0,
            ];
            // 如果价格表中有价格就使用价格表
            if (!empty($new_Roomprice[$daydate])) {
                $price_one = $new_Roomprice[$daydate];
                $pinfo     = [
                    'id'             => $i,
                    'room_id'        => $room_id,
                    'calendar_date'  => $daydate,
                    'mprice'         => $price_one['mprice'],
                    'total_num'      => $info->total_num,
                    'open_status'    => $price_one['open_status'],
                    'booking_status' => $price_one['booking_status'],
                ];
            }
            // 检查是否满房
            $statusk = Room::getBookingDateIsFM($room_id, $daydate);
            if ($statusk !== true) {
                $pinfo['open_status'] = 0;
            }
            $calendar_arr[] = $pinfo;
        }
        return $calendar_arr;
    }

    /**
     * @desc 获取房型客房销售SKU 日历价格列表 无限制
     * @param $room_id
     * @param int $is_cache
     * @param bool $days_num
     * @return array|mixed
     * author eRic
     * dateTime 2024-04-09 21:15
     */
    public static function getRoomSkuCalendarPrice($hotel_id, $room_sku_id, $is_cache = 1, $days_num = 90) {
        $sku_info      = RoomSkuPrice::where(['hotel_id' => $hotel_id, 'id' => $room_sku_id])->first();
        $room_id       = !empty($sku_info->room_id) ? $sku_info->room_id : 0;
        $info          = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();
        $where         = [
            ['hotel_id', '=', $hotel_id],
            ['room_id', '=', $room_id],
            ['room_sku_id', '=', $room_sku_id],
            ['calendar_date', '>=', Carbon::now()->toDateString()],
            ['calendar_date', '<=', date('Y-m-d', strtotime('+' . $days_num . ' day'))]
        ];
        $parm          = Roomprice::where($where)->select('room_id', 'calendar_date', 'mprice', 'open_status', 'booking_status')->get();
        $new_Roomprice = []; // 存放价格表信息
        if ($parm) {
            foreach ($parm as $items) {
                // 使用日期做为键名
                $new_Roomprice[$items['calendar_date']] = collect($items)->toArray();
            }
        }
        $calendar_arr = [];
        $currentDate  = date('Y-m-d'); // 获取当前日期
        for ($i = 0; $i <= $days_num; $i++) {
            $daydate = date('Y-m-d', strtotime($currentDate . ' +' . $i . ' day')); // 加一天
            $pinfo   = [
                'id'             => $i,
                'room_id'        => $room_id,
                'room_sku_id'    => $room_sku_id,
                'calendar_date'  => $daydate,
                'mprice'         => formatFloat($sku_info->roomsku_price),
                'total_num'      => $sku_info->roomsku_stock,
                'open_status'    => 1,
                'booking_status' => 0,
            ];
            // 如果价格表中有价格就使用价格表
            if (!empty($new_Roomprice[$daydate])) {
                $price_one = $new_Roomprice[$daydate];
                $pinfo     = [
                    'id'             => $i,
                    'room_id'        => $room_id,
                    'room_sku_id'    => $room_sku_id,
                    'calendar_date'  => $daydate,
                    'mprice'         => formatFloat($price_one['mprice']),
                    'total_num'      => $sku_info->roomsku_stock,
                    'open_status'    => $price_one['open_status'],
                    'booking_status' => $price_one['booking_status'],
                ];
            }
            // 检查是否满房
            $statusk = RoomSkuPrice::getBookingSkuDateIsFM($room_sku_id, $daydate);
            if ($statusk !== true) {
                $pinfo['open_status'] = 0;
            }
            $calendar_arr[] = $pinfo;
        }
        return $calendar_arr;
    }

    /**
     * @desc 获取客房日历价格列表
     * @param $room_id
     * @param int $is_cache
     * @param bool $days_num
     * @return array|mixed
     * author eRic
     * dateTime 2024-04-09 21:15
     */
    public static function getRoomDaysPricelist($room_id, $is_cache = 1, $days_num = 300) {
        $chache_keys = 'room_' . $room_id . '_price';
        $where       = [
            ['room_id', '=', $room_id],
            ['calendar_date', '>=', Carbon::now()->toDateString()],
            ['calendar_date', '<=', date('Y-m-d', strtotime('+' . $days_num . ' day'))]
        ];
        if ($is_cache == 1) {
            $parm = \Cache::get($chache_keys);
        } else {
            $parm = [];
        }
        if (empty($parm)) {
            $parm = Roomprice::where($where)->select('room_id', 'calendar_date', 'mprice', 'open_status', 'booking_status')->get();
            \Cache::put($chache_keys, $parm->toArray());
        }
        return $parm;
    }


    /**
     * @desc 获取客房sku日历价格列表
     * @param $room_id
     * @param int $is_cache
     * @param bool $days_num
     * @return array|mixed
     * author eRic
     * dateTime 2024-04-09 21:15
     */
    public static function getRoomSkuDaysPricelist($room_sku_id, $is_cache = 1, $days_num = 300) {
        $chache_keys = 'room_sku_' . $room_sku_id . '_price';
        $where       = [
            ['room_sku_id', '=', $room_sku_id],
            ['calendar_date', '>=', Carbon::now()->toDateString()],
            ['calendar_date', '<=', date('Y-m-d', strtotime('+' . $days_num . ' day'))]
        ];
        if ($is_cache == 1) {
            $parm = \Cache::get($chache_keys);
        } else {
            $parm = [];
        }
        if (empty($parm)) {
            $parm = Roomprice::where($where)->select('room_id', 'room_sku_id', 'calendar_date', 'mprice', 'open_status', 'booking_status')->get();
            \Cache::put($chache_keys, $parm->toArray());
        }
        return $parm;
    }

    // 关闭或打开某天的预定
    public static function setRoomDaysOpenStatus($room_id, $days, $open_otatus, $type = 'online_price') {
        $status = self::where(['room_id' => $room_id, 'type' => $type, 'calendar_date' => $days])->update(['open_otatus' => $open_otatus]);
        return '';
    }

    // 保存日历价格
    public static function addDaysPice($hotel_id, $room_id, $type, $days, $price, $open_status, $tiaojia_fangshi = 'calendar', $tiaojia_type = 0, $tiaojia_logid = 0) {
        $info    = self::where(['room_id' => $room_id, 'type' => $type, 'calendar_date' => $days])->count();
        $insdata = [
            'hotel_id'        => $hotel_id,
            'type'            => $type,
            'calendar_date'   => $days,
            'room_id'         => $room_id,
            'mprice'          => $price,
            'tiaojia_fangshi' => $tiaojia_fangshi,
            'tiaojia_type'    => $tiaojia_type,
            'tiaojia_logid'   => $tiaojia_logid,
            //'total_num'     => $total_num,
            'open_status'     => $open_status,
        ];
        if (!$info) {
            $model = self::create($insdata);
            return $model;
        }
        if (!empty($tiaojia_type) && $tiaojia_type == 1) { // 周末权重最小，不更新

        } else {
            $status = self::where(['room_id' => $room_id, 'type' => $type, 'calendar_date' => $days])->update($insdata);
        }

        // 重新生成缓存
        self::getRoomDaysPricelist($room_id, 0);

        return true;
    }

    // 保存sku日历价格
    public static function addSkuDaysPice($hotel_id, $room_id, $room_sku_id, $type, $days, $price, $open_status, $tiaojia_fangshi = 'calendar', $tiaojia_type = 0, $tiaojia_logid = 0) {
        $info    = self::where(['room_id' => $room_id, 'room_sku_id' => $room_sku_id, 'type' => $type, 'calendar_date' => $days])->count();
        $insdata = [
            'hotel_id'        => $hotel_id,
            'type'            => $type,
            'calendar_date'   => $days,
            'room_id'         => $room_id,
            'room_sku_id'     => $room_sku_id,
            'mprice'          => $price,
            'tiaojia_fangshi' => $tiaojia_fangshi,
            'tiaojia_type'    => $tiaojia_type,
            'tiaojia_logid'   => $tiaojia_logid,
            //'total_num'     => $total_num,
            'open_status'     => $open_status,
        ];
        if (!$info) {
            $model = self::create($insdata);
            return $model;
        }
        if (!empty($tiaojia_type) && $tiaojia_type == 1) { // 周末权重最小，不更新

        } else {
            $status = self::where(['room_id' => $room_id, 'room_sku_id' => $room_sku_id, 'type' => $type, 'calendar_date' => $days])->update($insdata);
        }

        // 重新生成缓存
        self::getRoomSkuDaysPricelist($room_sku_id, 0);

        return true;
    }

    /**
     * @desc 获取 客房预定优惠后的价格
     * @param $room_id
     * @param $start_time
     * @param $end_time
     * author eRic
     * dateTime 2024-06-09 18:47
     */
    public static function getRoomBookingRangeYouhuiPrice($user_id, $hotel_id, $room_id, $start_time, $end_time, $booking_num = 1) {
        $info        = Room::where(['hotel_id' => $hotel_id, 'id' => $room_id])->first();
        $total_price = Roomprice::getRoomDateRangePrice($room_id, $start_time, $end_time);
        if (empty($total_price)) {
            $total_price = $info->price;
        }
        $total_price = bcmul($total_price, $booking_num, 2); // 乘以预订间数
        $data        = [
            'youhui_price' => '',
            'yuan_price'   => $total_price,
            'total_price'  => $total_price,
            'coupon_list'  => [],
        ];

        // 查看优惠券
        $where       = [
            ['user_id', '=', $user_id],
            ['hotel_id', '=', $hotel_id],
            ['coupon_status', '=', 0],
            ['expire_time', '>=', date('Y-m-d H:i:s')],
        ];
        $coupon_user = CouponUser::where($where)->first();
        if ($coupon_user) {
            $couponinfo = Coupon::where(['id' => $coupon_user->coupon_id])->first();
            if (!empty($couponinfo->id)) {
                if (bccomp($total_price, $couponinfo->need_cost) == -1) { // 如何小于，则没有满足金额

                } else {


                    $yuan_price = $total_price;
                    $pay_price  = bcsub($total_price, $couponinfo->cost, 2);
                    $data       = [
                        'coupon_list'  => Coupon::where(['id' => $coupon_user->coupon_id])->get(),
                        'youhui_price' => $couponinfo->cost,
                        'yuan_price'   => $yuan_price,
                        'total_price'  => $pay_price,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @desc 获取 客房预定优惠后的价格
     * @param $room_id
     * @param $start_time
     * @param $end_time
     * author eRic
     * dateTime 2024-06-09 18:47
     */
    public static function getRoomSkuBookingRangeYouhuiPrice($user_id, $hotel_id, $room_sku_id, $start_time, $end_time, $booking_num = 1) {
        $info        = RoomSkuPrice::where(['hotel_id' => $hotel_id, 'id' => $room_sku_id])->first();
        $total_price = Roomprice::getRoomSkuDateRangePrice($room_sku_id, $start_time, $end_time);

        if (empty($total_price)) {
            $total_price = $info->roomsku_price;
        }
        $total_price = bcmul($total_price, $booking_num, 2); // 乘以预订间数
        $total_price = formatFloat($total_price);

        $calculator = new \App\Services\PriceCalculator($total_price);
        $calculator->calculateFinalPrice($user_id);

        // 获取最终价格和折扣信息
        $finalPrice = $calculator->getFinalPrice();
        $youhui_price = $calculator->getYouhuiPrice();
        $discounts = $calculator->getDiscounts();

        $data        = [
            'youhui_price' => $youhui_price,
            'yuan_price'   => $total_price,
            'total_price'  => $finalPrice,
            'coupon_list'  => $discounts,
        ];
        return $data;


        // 查看优惠券
        $where       = [
            ['user_id', '=', $user_id],
            ['hotel_id', '=', $hotel_id],
            ['coupon_status', '=', 0],
            ['expire_time', '>=', date('Y-m-d H:i:s')],
        ];
        $coupon_user = CouponUser::where($where)->first();
        if ($coupon_user) {
            $couponinfo = Coupon::where(['id' => $coupon_user->coupon_id])->first();
            if (!empty($couponinfo->id)) {
                if (bccomp($total_price, $couponinfo->need_cost) == -1) { // 如何小于，则没有满足金额

                } else {

                    $yuan_price = $total_price;
                    $pay_price  = bcsub($total_price, $couponinfo->cost, 2);
                    $data       = [
                        'coupon_list'  => Coupon::where(['id' => $coupon_user->coupon_id])->get(),
                        'youhui_price' => $couponinfo->cost,
                        'yuan_price'   => formatFloat($yuan_price),
                        'total_price'  => formatFloat($pay_price),
                    ];
                }
            }
        }

        return $data;
    }
}
