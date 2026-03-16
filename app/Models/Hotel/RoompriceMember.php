<?php

namespace App\Models\Hotel;

use Carbon\Carbon;

class RoompriceMember extends HotelBaseModel {

    protected $table = 'roomprice_member';
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
    public function setMoreimgAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['moreimg'] = json_encode($image);
        }
    }
    public function getMoreimgAttribute($image)
    {
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

    public static function calendar($year,$month,$days = '') {
        //获取年
        //$year = $request->input('year', now()->year);
        //获取月份
        //$month     = $request->input('month', now()->month);
        if(empty($days)){
            $days = date('d');
        }
        $yearMonth = sprintf("%d-%s", $year, $month);
        $yearMonthDays = $yearMonth.'-'.$days;
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
    public static function calendars($year,$month)
    {
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
}
