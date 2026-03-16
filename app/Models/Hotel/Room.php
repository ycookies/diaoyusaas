<?php

namespace App\Models\Hotel;

use Carbon\Carbon;
use Dcat\Admin\Admin;

class Room extends HotelBaseModel {

    protected $table = 'room';
    protected $guarded = [];
    /*protected $appends = ['base_sheshi'];
    public function getBaseSheshiAttribute() {
        $sheshi = [
          '网络通讯' =>  $this->wifi_network,
          '客房布局' => $this->kefang_buju,
          '洗浴用品' =>  $this->xiyu_yongpin,
          '客房设施' =>  $this->kefang_sheshi,
          '食品饮品' =>  $this->shipin_yinpin,
          '媒体科技' =>  $this->meiti_keji,
          '清洁服务' =>  $this->qingjie_fuwu,
          '便利设施' =>  $this->bianli_sheshi,
        ];
        //$list = RoomSheshi::where(['hotel_id'=>$this->hotel_id])->get();
        return $sheshi;
    }*/

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

    /*public function seller(){
        return $this->hasOne(\App\Merchant\Models\MerchantUser::class,'id','seller_id');
    }*/
    public static $wifi_network = [
        '客房WIFI覆盖' => '客房WIFI覆盖', '电话' => '电话', '客房有线宽带' => '客房有线宽带'
    ];

    public static $kefang_buju = [
        '书桌' => '书桌', '沙发' => '沙发', '衣柜/衣橱' => '衣柜/衣橱'
    ];
    public static $xiyu_yongpin = [
        '毛巾' => '毛巾', '牙膏' => '牙膏', '牙刷' => '牙刷', '洗发水' => '洗发水', '沐浴露' => '沐浴露'
    ];
    public static $kefang_sheshi = [
        '自动窗帘' => '自动窗帘', '遮光窗帘' => '遮光窗帘', '床具:鸭绒被' => '床具:鸭绒被', '床具:毯子或被子' => '床具:毯子或被子', '开夜灯' => '开夜灯'
    ];

    public static $shipin_yinpin = [
        '瓶装水' => '瓶装水', '电热水壶' => '电热水壶'
    ];

    public static $meiti_keji = [
        '电视机' => '电视机', '有线频道' => '有线频道', '智能门锁' => '智能门锁'
    ];

    public static $qingjie_fuwu = [
        '熨衣设备' => '熨衣设备', '打扫工具' => '打扫工具'
    ];
    public static $bianli_sheshi = [
        '针线包' => '针线包', '衣架' => '衣架', '多种规格电源插座' => '多种规格电源插座', '220V电压插座' => '220V电压插座', '雨伞' => '雨伞'
    ];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    // 网络与通讯
    public function setWifiNetworkAttribute($value) {
        if (is_array($value)) {
            $this->attributes['wifi_network'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getWifiNetworkAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setKefangBujuAttribute($value) {
        if (is_array($value)) {
            $this->attributes['kefang_buju'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getKefangBujuAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setXiyuYongpinAttribute($value) {
        if (is_array($value)) {
            $this->attributes['xiyu_yongpin'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getXiyuYongpinAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setKefangSheshiAttribute($value) {
        if (is_array($value)) {
            $this->attributes['kefang_sheshi'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getKefangSheshiAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setShipinYinpinAttribute($value) {
        if (is_array($value)) {
            $this->attributes['shipin_yinpin'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getShipinYinpinAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setMeitiKejiAttribute($value) {
        if (is_array($value)) {
            $this->attributes['meiti_keji'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getMeitiKejiAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setQingjieFuwuAttribute($value) {
        if (is_array($value)) {
            $this->attributes['qingjie_fuwu'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getQingjieFuwuAttribute($value) {
        return json_decode($value, true);
    }

    // 网络与通讯
    public function setBianliSheshiAttribute($value) {
        if (is_array($value)) {
            $this->attributes['bianli_sheshi'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getBianliSheshiAttribute($value) {
        return json_decode($value, true);
    }

    //
    public function setAgreementPriceStatusAttribute($value) {
        if (is_array($value)) {
            $this->attributes['agreement_price_status'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    public function getAgreementPriceStatusAttribute($value) {
        return json_decode($value, true);
    }


    public function setMoreimgAttribute($image) {
        if (is_array($image)) {
            $this->attributes['moreimg'] = json_encode($image);
        }
    }

    public function getMoreimgAttribute($image) {
        return json_decode($image, true);
    }

    /**
     * @desc
     * @param $days 日期
     * @param $room_id 房型ID
     * @param array $price 价格
     * @param $room_num 房间数
     * @param $zaocan_num 早餐数量
     * @return string
     * author eRic
     * dateTime 2024-06-08 17:05
     */
    public static function showDaysPrice($days, $room_id, array $price,$room_num,$zaocan_num) {

        $open_status = $price['open_status'];
        $online_price = $price['online_price'];
        $htmls = "<div class='text price_set' data-type = 'online_price' data-type-name = '线上价' data-days='$days' data-room_id='$room_id' data-open_status='$open_status' data-price='$online_price'>";
        if (!empty($price['open_status'])) {
            if($room_num <= 0){
                $htmls .= "<div class=' f12 text text-center booking-full' >已满房</div>";
            }else{
                $htmls .= "<div class='f12 text text-center text-success price_bg'>可预订</div>";
            }
        } else {
            $htmls .= "<div class='f12 text text-center booking-danger'>已关闭</div>";
        }

        $htmls .= "<div class='f12 text-muted text text-center tips' data-title='房量'>剩 ".$room_num."</div>";
        $htmls .= "<div class='fline text-muted'>------</div>";
        $htmls .= "<div class='f14 text-muted text text-center tips' data-title='当日卖价'> ￥" . $price['online_price'] . "</div>";
        $htmls .= "<div class='f12 text-muted text text-center tips' data-title='餐食'><img src='/img/zaocan.png' width='22' /> $zaocan_num</div>";
        /*if (!empty($price['booking_status'])) {
            $htmls .= "<div class='text text-center text-success price_bg'>已开房</div>";
        }*/
        $htmls .= '</div>';
        /*$htmls .= "<a class='price_set' href='#' data-type = 'Offline_price' data-type-name = '门市价' data-days='$days' data-room_id='$room_id' data-price='400.00'>门市价:400.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'xieyi_price' data-type-name = '协议价' data-days='$days' data-room_id='$room_id' data-price='344.00'>协议价:344.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'vip_price' data-type-name = '会员价' data-days='$days' data-room_id='$room_id' data-price='388.00'>会员价:388.00</a><br>";
        */
        return $htmls;
    }

    /**
     * @desc sku
     * @param $days 日期
     * @param $room_id 房型ID
     * @param array $price 价格
     * @param $room_num 房间数
     * @param $zaocan_num 早餐数量
     * @return string
     * author eRic
     * dateTime 2024-06-08 17:05
     */
    public static function showSkuDaysPrice($days, $room_id,$room_sku_id, array $price,$room_num,$zaocan_num) {

        $open_status = $price['open_status'];
        $online_price = $price['online_price'];
        $htmls = "<div class='text price_set' data-type = 'online_price' data-type-name = '线上价' data-days='$days' data-room_id='$room_id' data-room_sku_id='$room_sku_id' data-open_status='$open_status' data-price='$online_price'>";
        if (!empty($price['open_status'])) {
            if($room_num <= 0){
                $htmls .= "<div class=' f12 text text-center booking-full' >已满房</div>";
            }else{
                $htmls .= "<div class='f12 text text-center text-success price_bg'>可预订</div>";
            }
        } else {
            $htmls .= "<div class='f12 text text-center booking-danger'>已关闭</div>";
        }

        $htmls .= "<div class='f12 text-muted text text-center tips' data-title='房量'>剩 ".$room_num."</div>";
        $htmls .= "<div class='fline text-muted'>------</div>";
        $htmls .= "<div class='f14 text-muted text text-center tips' data-title='当日卖价'> ￥" . $price['online_price'] . "</div>";
        $htmls .= "<div class='f12 text-muted text text-center tips' data-title='餐食'><img src='/img/zaocan.png' width='22' /> $zaocan_num</div>";
        /*if (!empty($price['booking_status'])) {
            $htmls .= "<div class='text text-center text-success price_bg'>已开房</div>";
        }*/
        $htmls .= '</div>';
        /*$htmls .= "<a class='price_set' href='#' data-type = 'Offline_price' data-type-name = '门市价' data-days='$days' data-room_id='$room_id' data-price='400.00'>门市价:400.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'xieyi_price' data-type-name = '协议价' data-days='$days' data-room_id='$room_id' data-price='344.00'>协议价:344.00</a><br>";
        $htmls .= "<a class='price_set' href='#' data-type = 'vip_price' data-type-name = '会员价' data-days='$days' data-room_id='$room_id' data-price='388.00'>会员价:388.00</a><br>";
        */
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
        // 酒店客房维护的最大天数
        $configarr = HotelSetting::getlists(['max_room_price_set_num'], Admin::user()->hotel_id);
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

    // 生成日期
    public static function makeDate($yearMonth) {
        $dates = [];
        if ($yearMonth == date('Y-m')) {
            $firstDay = strtotime(date('Y-m-d'));
        } else {
            $firstDay = strtotime($yearMonth . '-01');
        }

        $lastDay = strtotime(date('Y-m-t', $firstDay));
        $i       = 0;
        for ($day = $firstDay; $day <= $lastDay; $day += 86400) {
            $date     = date('Y-m-d', $day);
            $week     = date('N', $day);
            $weekdays = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
            $weekDay  = $weekdays[$week - 1];
            $dates[]  = ['date' => $date, 'week' => $weekDay];
            $i++;
            if ($i > 40) {
                break;
            }
        }

        return $dates;
    }


    // 生成客户的日历价格
    public static function makeRoomDaysPrice($hotel_id, $room_id, $type = 'online_price') {
        $info        = Room::where(['id' => $room_id])->select('price', 'total_num')->first();
        $currentDate = date('Y-m-d'); // 获取当前日期
        for ($i = 0; $i <= 90; $i++) {
            $daydate = date('Y-m-d', strtotime($currentDate . ' +' . $i . ' day')); // 加一天
            Roomprice::addDaysPice($hotel_id, $room_id, $type, $daydate, $info->price, 1);
        }
    }

    // 获取客房日期的价格
    public function getRoomDayPrice($room_id, $days_date) {

    }

    // 获取早餐数量
    public static function getZaocanNum($model){
        $zaocan_num = 0;
        if(!empty($model->breakfast)){
            $zaocan_num = !empty($model->zaocan_num) ? $model->zaocan_num:0;
        }
        return $zaocan_num;
    }

    /**
     * @desc 获取客房预定日期范围内 每天是否都有房量
     * @param $room_id 房间ID
     * @param $arrival_time 入驻时间
     * @param $departure_time 离店时间
     * @return array
     * author eRic
     * dateTime 2024-06-09 00:04
     */
    public static function getBookingRangeIsFM($room_id,$arrival_time,$departure_time){
        $info = self::where(['id'=> $room_id])->first();
        $departure_time = date('Y-m-d',strtotime($departure_time." -1 day")); // 去掉离店当天
        $booking_date_range = getDatesInRange($arrival_time,$departure_time);
        $booking_date_range_list =[];
        $tips = '';
        foreach ($booking_date_range as $key => $datetime) {
            $num = self::getRoomNum($info,$datetime);
            $booking_date_range_list[$datetime] = $num;
            if(empty($num)){
                $tips = $datetime.':已满房 无法预订';
                break;
            }
        }
        if(!empty($tips)){
            return $tips;
        }
        return true;
    }

    /**
     * @desc 获取客房预定日期范围内 每天是否都有房量
     * @param $room_id 房间ID
     * @param $arrival_time 入驻时间
     * @param $departure_time 离店时间
     * @return array
     * author eRic
     * dateTime 2024-06-09 00:04
     */
    public static function getBookingSkuRangeIsFM($room_sku_id,$arrival_time,$departure_time){
        $info = RoomSkuPrice::where(['id'=> $room_sku_id])->first();
        $departure_time = date('Y-m-d',strtotime($departure_time." -1 day")); // 去掉离店当天
        $booking_date_range = getDatesInRange($arrival_time,$departure_time);
        $booking_date_range_list =[];
        $tips = '';
        foreach ($booking_date_range as $key => $datetime) {
            $num = RoomSkuPrice::getRoomNum($info,$datetime);
            $booking_date_range_list[$datetime] = $num;
            if(empty($num)){
                $tips = $datetime.':已满房 无法预订';
                break;
            }
        }
        if(!empty($tips)){
            return $tips;
        }
        return true;
    }

    /**
     * @desc 检查某一天 是否满房
     * @param $room_id 房间ID
     * @param $arrival_time 入驻时间
     * @param $departure_time 离店时间
     * @return array
     * author eRic
     * dateTime 2024-06-09 00:04
     */
    public static function getBookingDateIsFM($room_id,$datetime){
        $info = self::where(['id'=> $room_id])->first();
        $num = self::getRoomNum($info,$datetime);
        $tips = '';
        if(empty($num)){
            $tips = '已满房';
        }
        if(!empty($tips)){
            return $tips;
        }
        return true;
    }

    // 获取当日剩余房间
    public static function getRoomNum($model,$days_time){
        $total_num = $model->total_num;
        $booking_num = RoomBookingLog::where(['room_id'=>$model->id,'date_time'=>$days_time])->count();
        return bcsub($total_num,$booking_num,0);
    }
}
