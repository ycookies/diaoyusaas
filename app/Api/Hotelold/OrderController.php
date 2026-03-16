<?php

namespace App\Api\Hotelold;

use App\Models\Hotel\Room;
use App\Models\Hotel\WxappConfig;
use App\Services\WxUserService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
// use Orion\Http\Requests\Request;
use App\Http\Controllers\Controller;

// 酒店预定
class OrderController extends Controller {

    /**
     * 订单详情
     */
    public function model(Request $request){
        $hotel_id   = intval($request->input('hotel_id'));
        $model_id   = intval($request->input('model_id'));
        $time       = trim($request->input('time'));

        $hotel = Hotel::where(['company_id'=>$this->company_id, 'is_active'=>1])->whereIn('status', [2, 3])->find($hotel_id);
        if(empty($hotel)){
            return response()->json([
                'code'  => 500,
                'msg'   => '酒店信息不存在'
            ]);
        }

        $model = Model::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1, 'status'=>1])->find($model_id);
        if(empty($model)){
            return response()->json([
                'code'  => 500,
                'msg'   => '房型信息不存在'
            ]);
        }

        $arr_time   = explode("~", $time);
        if(count($arr_time) != 2){
            return response()->json([
                'code'  => 500,
                'msg'   => '日期不正确',
            ]);
        }

        $day1 = explode("-", $arr_time[0]);
        $day2 = explode("-", $arr_time[1]);

        $d1 = mktime(0,0,0, $day1[1], $day1[2], $day1[0]);
        $d2 = mktime(0,0,0, $day2[1], $day2[2], $day2[0]);

        # 时间差
        $days = round(($d2-$d1)/3600/24);

        if($days < 1){
            return response()->json([
                'code'  => 500,
                'msg'   => '日期不正确',
            ]);
        }

        // 最多订房间数
        $max = $model->book_num;
        for ($day=0; $day<=$days-1; $day++) {
            $date = date('Y-m-d', strtotime("+{$day} day", $d1));

            # 状态
            $book_num = $this->getModelBookStatus($this->company_id, $hotel_id, $model->id, strtotime($date));

            $max = ($book_num && $book_num <= $max) ? $book_num : $max;
        }

        $beds = [];
        if($model->bed){
            $beds = Bed::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1])->whereIn('id', explode(',', $model->bed))->pluck('name');
        }

        // 预定政策
        $setting_policy = Setting::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'key'=>'policy', 'is_active'=>1])->first();
        // 入住规则
        $setting_rule = Setting::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'key'=>'rule', 'is_active'=>1])->first();

        // 用户信息
        $user = User::find($this->user_id);
        $user_score = $user ? intval($user->score) : 0;

        // 积分判断
        $is_exchange = true;

        $user_score_status = Setting::where(['company_id'=>$this->company_id, 'key'=>'user_score_status', 'is_active'=>1])->first();
        if(empty($user_score_status) || empty($user_score_status->value)){
            $is_exchange = false;
        }

        // 判断是否配置支付
        $pay_setting = PaySetting::where(['company_id'=>$this->company_id, 'is_active'=>1])->first();
        $pay_open = $pay_setting ? intval($pay_setting->open) : 0;

        // 用户积分兑换金额
        $setting = Setting::where(['company_id'=>$this->company_id, 'key'=>'user_socre_money', 'is_active'=>1])->first();
        $user_socre_money = intval($setting->value);

        $score_money = $user_socre_money ? floor($user_score/$user_socre_money) : 0;

        $data = [
            'model_id'  => $model_id,
            'name'      => $model->name,
            'images'    => $this->ossUri($model->images),
            'area'      => $model->area,
            'window'    => $this->getConfig($model->is_window, 'model_window'),
            'breakfast' => $this->getConfig($model->is_breakfast, 'model_breakfast'),
            'bed'       => $beds,
            'max'       => $max,
            'policy'    => $setting_policy ? $setting_policy->value : '',
            'rule'      => $setting_rule ? $setting_rule->value : '',
            'is_exchange' => $is_exchange,
            'score_money' => $score_money,
            'score' =>  $score_money * $user_socre_money,
            'pay_open'  => $pay_open ? true : false
        ];

        return response()->json([
            'code'  => 200,
            'msg'   => $data
        ]);
    }

    /**
     * 优惠券及满减券兑换
     */
    public function ticket(Request $request){
        $hotel_id   = intval($request->input('hotel_id'));
        $model_id   = intval($request->input('model_id'));
        $time       = trim($request->input('time'));

        # 所有日期
        $days = $this->getDayBetween($time);

        $weekdays = [];
        foreach ($days as $day){
            $weekdays[] = date('N', strtotime($day));
        }

        // 总共房价金额
        $my_tockets = TicketGive::where(['company_id'=>$this->company_id, 'user_id'=>$this->user_id, 'is_active'=>1, 'status'=>0])->orderBy('id', 'DESC')->get();

        $ticket_valid   = [];
        $ticket_vain    = [];
        foreach ($my_tockets as $row){
            $ticket = Ticket::where(['company_id'=>1, 'is_active'=>1])->find($row->ticket_id);

            # 优惠券不存在
            if(empty($ticket)){
                continue;
            }

            # 房型
            $ticket_models = TicketHotel::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'ticket_id'=>$row->ticket_id, 'is_active'=>1])->first();
            if(empty($ticket_models)){
                continue;
            }
            $model_ids = explode(",", $ticket_models->model_ids);

            $models = Model::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'status'=>1, 'is_active'=>1])->whereNotIn('id', $model_ids)->count('id');
            # 全部房型
            if(empty($models)){
                $model_name = "全部房型";
            }else{
                $model_name = Model::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'status'=>1, 'is_active'=>1])
                    ->whereIn('id', $model_ids)
                    ->pluck('name')
                    ->toArray();
                $model_name = is_array($model_name) ? implode(",", $model_name) : $model_name;
            }

            $values = explode(",", $ticket->use_value);

            $info = [
                'tg_id'     => $row->id,
                'name'      => $ticket->name,
                'time'      => empty($row->start_at) ? '长期' : date('Y.m.d', $row->start_at) . "-" . date('Y.m.d', $row->start_at),
                'model_name' => $model_name,
                'use_type'  => $ticket->use_type == 1 ? '无门槛' : "满{$values[1]}元可用",
                'use_value' => $values[0]
            ];

            $start_time = strtotime($days[0] . " 14:00:00");
            $end_time   = strtotime($days[count($days)-1] . " 12:00:00");

            # 有效时间
            if($row->start_at && $row->end_at){
                if($start_time > $row->end_at || $end_time < $row->start_at){
                    $info['step'] = 1;
                    $ticket_vain[] = $info;
                    continue;
                }
            }

            # 优惠券使用房型
            $ticket_hotel = TicketHotel::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'ticket_id'=>$row->ticket_id, 'is_active'=>1])
                ->whereRaw('FIND_IN_SET('.$model_id.',`model_ids`)')->first();

            # 不可用优惠券
            if(empty($ticket_hotel)) {
                $info['step'] = 2;
                $ticket_vain[] = $info;
                continue;
            }

            /**
             * 用券时间判断
             * 1：先判断节假日情况
             * 2：再判断有效日情况
             */

            $holiday = Holiday::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1])
                ->where('start_at', '<', $start_time)
                ->where('end_at', '>', $end_time)
                ->first();

            # 节假日不在使用范围内
            if($holiday && !in_array($holiday->id, explode(',', $ticket->use_holiday))){
                $info['step'] = 3;
                $ticket_vain[] = $info;
                continue;
            }

            $use_week = explode(",", $ticket->use_week);

            # 非使用日期
//            if(!array_intersect($use_week, $weekdays)){
//                $info['step'] = 4;
//                $ticket_vain[] = $info;
//                continue;
//            }

            $ticket_valid[] = $info;
        }

        return response()->json([
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => [
                'ticket_valid'  => $ticket_valid,
                'ticket_vain'   => $ticket_vain,
            ]
        ]);
    }

    /**
     * 费用明细
     */
    public function trade(Request $request){
        $hotel_id   = intval($request->input('hotel_id'));
        $model_id   = intval($request->input('model_id'));
        $model_num  = intval($request->input('model_num'));
        $time       = trim($request->input('time'));
        $tg_id      = intval($request->input('tg_id'));
        $score      = intval($request->input('score'));

        $hotel = Hotel::where(['company_id'=>$this->company_id, 'is_active'=>1])->whereIn('status', [2, 3])->find($hotel_id);
        if(empty($hotel)){
            return response()->json([
                'code'  => 500,
                'msg'   => '酒店信息不存在'
            ]);
        }

        $model = Model::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1, 'status'=>1])->find($model_id);
        if(empty($model)){
            return response()->json([
                'code'  => 500,
                'msg'   => '房型信息不存在'
            ]);
        }

        # 所有日期
        $days = $this->getDayBetween($time);

        # 需要支付的费用
        $pay_money = 0;

        # 赠送积分
        $giv = 0;

        $res = [];

        # 房间费用
        foreach ($days as $day){
            $money = $this->getModelPrice($this->company_id, $hotel_id, $model_id, $day);

            for ($num=1; $num<=$model_num; $num++){
                $title = "房间" . ($model_num > 1 ? $num : '') . "({$day})";

                $pay_money += $money;

                $res[] = [
                    'title' => $title,
                    'money' => "￥" . $money,
                    'color' => '#d43030'
                ];
            }
        }

        # 优惠券
        if($tg_id){
            $ticket_give = TicketGive::where(['company_id'=>$this->company_id, 'user_id'=>$this->user_id, 'status'=>0, 'is_active'=>1])->find($tg_id);

            if($ticket_give){
                $ticket = Ticket::find($ticket_give->ticket_id);
                $values = explode(",", $ticket->use_value);

                $pay_money -= $values[0];

                $res[] = [
                    'title' => "优惠券({$ticket->name})",
                    'money' => "-￥" . $values[0],
                    'color' => '#43cf7c'
                ];
            }
        }

        $setting = Setting::where(['company_id'=>$this->company_id, 'key'=>'user_socre_money', 'is_active'=>1])->first();
        $user_socre_money = intval($setting->value);

        # 最多需要抵扣的积分
        $max_score = $pay_money * $user_socre_money;

        # 积分兑换
        if($score){
            $user = User::find($this->user_id);
            if($user->score >= $score){

                $pay_money -= intval($score/$user_socre_money);

                if($user_socre_money){
                    $res[] = [
                        'title' => "积分兑换",
                        'money' => "-￥" .intval($score/$user_socre_money),
                        'color' => '#43cf7c'
                    ];
                }
            }
        }

        #
        $setting2 = Setting::where(['company_id'=>$this->company_id, 'key'=>'user_money_socre', 'is_active'=>1])->first();
        $user_money_socre = intval($setting2->value);

        return response()->json([
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $res,
            'max_score' => $max_score,
            'pay_money' => $pay_money,
            'give_score' => intval($user_money_socre * $pay_money)
        ]);
    }

    /**
     * 创建订单
     */
    public function book(Request $request){
        $hotel_id   = intval($request->input('hotel_id'));
        $model_id   = intval($request->input('model_id'));
        $model_num  = intval($request->input('model_num'));
        $time       = trim($request->input('time'));
        $tg_id      = intval($request->input('tg_id'));
        $score      = intval($request->input('score'));
        $mobile     = trim($request->input('mobile'));
        $plan       = intval($request->input('plan'));
        $username   = trim($request->input('username'));

        if(empty($model_num)){
            return response()->json([
                'code'  => 500,
                'msg'   => '至少选择一间房间',
            ]);
        }

        $arr_username = explode(",", $username);
        if(count($arr_username) < $model_num){
            return response()->json([
                'code'  => 500,
                'msg'   => '用户信息数填写不完整',
            ]);
        }

        if(!preg_match("/^1[3456789]{1}\d{9}$/",$mobile)){
            return response()->json([
                'code'  => 500,
                'msg'   => '请填写正确电话',
            ]);
        }

        $hotel = Hotel::where(['company_id'=>$this->company_id, 'is_active'=>1])->whereIn('status', [2, 3])->find($hotel_id);
        if(empty($hotel)){
            return response()->json([
                'code'  => 500,
                'msg'   => '酒店信息不存在',
                'data'  => [
                    'company_id'    => $this->company_id,
                    'hotel_id'      => $hotel_id
                ]
            ]);
        }

        $model = Model::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1, 'status'=>1])->find($model_id);
        if(empty($model)){
            return response()->json([
                'code'  => 500,
                'msg'   => '房型信息不存在'
            ]);
        }

        # 所有日期
        $days = $this->getDayBetween($time);

        # 订单数量
        $max = $model->book_num;
        foreach ($days as $day){
            # 状态
            $book_num = $this->getModelBookStatus($this->company_id, $hotel_id, $model->id, strtotime($day));

            $max = ($book_num && $book_num < $max) ? $book_num : $max;
        }

        if($model_num > $max){
            return response()->json([
                'code'  => 500,
                'msg'   => '房间数量不足，请重新下单'
            ]);
        }

        # 入住时间
        $setting_checkin_time = Setting::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'key'=>'checkin_time', 'is_active'=>1])->first();
        $checkin_time = $setting_checkin_time ? $setting_checkin_time->value : "14:00";

        # 离开时间
        $setting_checkout_time = Setting::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'key'=>'checkout_time', 'is_active'=>1])->first();
        $checkout_time = $setting_checkout_time ? $setting_checkout_time->value : "12:00";

        // 判断是否配置支付
        $pay_setting = PaySetting::where(['company_id'=>$this->company_id, 'is_active'=>1])->first();
        $pay_open = $pay_setting ? intval($pay_setting->open) : 0;

        $arr_time = explode("~", $time);


        // 订单状态规则
        if(date('Y-m-d', strtotime($arr_time[0])) == date('Y-m-d')){ # 预订今日
            $pay_status = $pay_open == 1 ? 1 : 3;
        }else{
            $pay_status = $pay_open == 1 ? 1 :2;
        }

        // 订单开始时间
        $start_at   = strtotime($arr_time[0] . " {$checkin_time}");
        if($start_at < time()){
            $start_at = time();
        }

        // 订单结束时间
        $end_at = strtotime($arr_time[1] . " {$checkout_time}");

        $book_ids = [];

        for($num=1; $num<=$model_num; $num++){
            $objBook = new Book();
            $objBook->code          = $this->makeBookCode($hotel_id);
            $objBook->company_id    = $this->company_id;
            $objBook->hotel_id      = $hotel_id;
            $objBook->user_id       = $this->user_id;
            $objBook->status        = $pay_status;
            $objBook->start_at      = $start_at;
            $objBook->end_at        = $end_at;
            $objBook->start_date    = date('Y-m-d', $start_at);
            $objBook->end_date      = date('Y-m-d', strtotime("-1 day", $end_at));
            $objBook->model_id      = $model_id;
            $objBook->plan_at       = $plan;
            $objBook->day           = date('Ymd');
            $objBook->source        = '自有平台';
            $objBook->is_online     = 1; // bugfix
            $objBook->handover_id   = $hotel->handover_id;
            $objBook->save();

            $book_id = $objBook->id;
            $book_ids[] = $book_id;

            $this->dispatch(new CloseBook($objBook, config('app.book_ttl')));

            # book_checkin
            if(isset($arr_username[$num-1])){
                $objCheckin = new BookCheckin();
                $objCheckin->book_id    = $book_id;
                $objCheckin->company_id = $this->company_id;
                $objCheckin->hotel_id   = $hotel_id;
                $objCheckin->model_id   = $model_id;
                if($num == 1){
                    $objCheckin->user_id= $this->user_id;
                }
                $objCheckin->mobile     = $mobile;
                $objCheckin->username   = $arr_username[$num-1];
                $objCheckin->save();
            }

            # book_trade
            # 房间费用
            $money = 0;
            foreach ($days as $day){
                $money = $this->getModelPrice($this->company_id, $hotel_id, $model_id, $day);
            }
            $objTrade = new BookTrade();
            $objTrade->book_id      = $book_id;
            $objTrade->company_id   = $this->company_id;
            $objTrade->hotel_id     = $hotel_id;
            $objTrade->model_id     = $model_id;
            $objTrade->user_id      = $this->user_id;
            $objTrade->type         = 1;
            $objTrade->category     = 1;
            $objTrade->remark       = '房间费';
            $objTrade->table_name   = 'room';
            $objTrade->unit_price   = $money;
            $objTrade->amount       = 1;
            $objTrade->actual_money = $money;
            $objTrade->money        = $money;
            $objTrade->pay_status   = 0;
            $objTrade->content      = json_encode([['day'=>date('Y-m-d', $start_at)."~".date('Y-m-d', strtotime("-1 day", $end_at)), 'money'=>$money]]);
            $objTrade->save();

            # 优惠券及积分兑换
            if($num == 1){
                if($tg_id){
                    $ticket_give = TicketGive::where(['company_id'=>$this->company_id, 'user_id'=>$this->user_id, 'status'=>0, 'is_active'=>1])->find($tg_id);

                    if($ticket_give){
                        $ticket = Ticket::find($ticket_give->ticket_id);

                        $values = explode(",", $ticket->use_value);

                        $money = $values[0];

                        $objTrade = new BookTrade();
                        $objTrade->book_id      = $book_id;
                        $objTrade->company_id   = $this->company_id;
                        $objTrade->hotel_id     = $hotel_id;
                        $objTrade->model_id     = $model_id;
                        $objTrade->user_id      = $this->user_id;
                        $objTrade->type         = 2;
                        $objTrade->category     = 10;
                        $objTrade->remark       = '优惠券';
                        $objTrade->table_name   = 'ticket';
                        $objTrade->unit_price   = $money;
                        $objTrade->amount       = 1;
                        $objTrade->actual_money = $money;
                        $objTrade->money        = $money;
                        $objTrade->pay_status   = 0;
                        $objTrade->save();

                        $ticket_give->status = 1;
                        $ticket_give->trade_id = $objTrade->id;
                        $ticket_give->save();
                    }
                }

                # 积分兑换
                if($score){
                    $user = User::find($this->user_id);
                    if($user->score >= $score){
                        $objTrade = new BookTrade();
                        $objTrade->book_id      = $book_id;
                        $objTrade->company_id   = $this->company_id;
                        $objTrade->hotel_id     = $hotel_id;
                        $objTrade->model_id     = $model_id;
                        $objTrade->user_id      = $this->user_id;
                        $objTrade->type         = 2;
                        $objTrade->category     = 12;
                        $objTrade->remark       = '积分兑换';
                        $objTrade->table_name   = 'score';
                        $objTrade->unit_price   = $money;
                        $objTrade->amount       = 1;
                        $objTrade->actual_money = $money;
                        $objTrade->money        = $money;
                        $objTrade->pay_status   = 0;
                        $objTrade->save();

                        /**
                         * 扣除积分
                         * 记录
                         * 消息队列
                         * TODO
                         */
                        Book::where(['id'=>$book_id])->update(['score'=>$score]);
                    }
                }
            }
        }

        // 关联订单
        if(count($book_ids) > 1){
            $objBookRelation = new BookRelation();
            $objBookRelation->company_id    = $this->company_id;
            $objBookRelation->hotel_id      = $hotel_id;
            $objBookRelation->book_ids      = implode(",", $book_ids);
            $objBookRelation->save();

            $relation_id = $objBookRelation->id;

            Book::where(['company_id'=>$this->company_id, 'hotel_id'=>$hotel_id, 'is_active'=>1])->whereIn('id', $book_ids)->update(['relation_id'=>$relation_id]);
        }

        return response()->json([
            'code'  => 200,
            'msg'   => '创建成功',
            'data'  => [
                'book_id'   => $objBook->id,
                'relation'  => count($book_ids) ? true : false,
                'pay_open'  => $pay_open == 1 ? true :false
            ]
        ]);
    }

    /**
     * 在线支付
     */
    public function pay(Request $request){
        $book_id    = intval($request->input('book_id'));
        $relation   = boolval($request->input('relation', false));
        $book       = Book::where(['company_id'=>$this->company_id, 'is_active'=>1])->find($book_id);

        if(empty($book)){
            return response()->json([
                'code'  => 500,
                'msg'   => '订单不存在'
            ]);
        }

        $pay_setting = PaySetting::where(['company_id'=>$this->company_id, 'is_active'=>1])->first();

        if(empty($pay_setting) || $pay_setting->open != 1){
            return response()->json([
                'code'  => 500,
                'msg'   => '暂未开启支付',
            ]);
        }

        $wechat = Wechat::where(['company_id'=>$this->company_id, 'type'=>2, 'is_active'=>1])->first();
        if(empty($wechat)){
            return response()->json([
                'code'  => 500,
                'msg'   => '小程序未授权',
            ]);
        }

        $data = base64_decode($pay_setting->content, true);
        $pay_setting = unserialize($data);

        $config = [
            'app_id'        => $wechat->appid,
            'mch_id'        => $pay_setting['mch_id'],
            'key'           => $pay_setting['key'],   // API 密钥
            'notify_url'    => env('APP_URL') . '/hotel/pay/notify',
        ];

        $type = 1;
        if($relation && $book->relation_id){
            $relation = BookRelation::find($book->relation_id);
            $book_ids = explode(",", $relation->book_ids);
            $type = 2;
        }else{
            $book_ids = [$book_id];
        }

        /**
         * 需要支付金额明细
         */
        $book_trade_ids = [];

        $money = 0;
        foreach ($book_ids as $new_book_id){
            $book_trades = BookTrade::where(['company_id'=>$this->company_id, 'book_id'=>$new_book_id, 'is_active'=>1])->where('pay_status', '!=', 1)->get();
            foreach ($book_trades as $trade){
                $book_trade_ids[] = $trade->id;
                if($trade->type == 2){
                    $money -= $trade->money;
                }else{
                    $money += $trade->money;
                }
            }
        }

        /**
         * 生成支付订单
         */
        $pay_status = $money <= 0 ? 1 : 0;

        $objPay = new BookPay();
        $objPay->company_id = $book->company_id;
        $objPay->hotel_id   = $book->hotel_id;
        $objPay->book_id    = $book->id;
        $objPay->user_id    = $book->user_id;
        $objPay->money      = $money;
        $objPay->pay_status = $pay_status;
        $objPay->pay_method = 1;
        $objPay->source     = $book->source;
        $objPay->save();

        $pay_id = $objPay->id;

        BookTrade::whereIn('id', $book_trade_ids)->update(['pay_id'=>$pay_id, 'pay_status'=>$pay_status]);

        # 支付成功
        if($pay_status == 1){

            BookTrade::whereIn('id', $book_trade_ids)->update(['pay_id'=>$pay_id, 'pay_status'=>1, 'pay_at'=>time()]);

            /**
             * 扣除积分
             */
            // ConsumeScore::dispatch($book);

            return response()->json([
                'code'  => 200,
                'msg'   => '支付成功',
                'data'  => [
                    'pay_status' => 1
                ]
            ]);
        }

        $app = Factory::payment($config);
        $result = $app->order->unify([
            'body'          => '酒店预订',
            'out_trade_no'  => $book->code . "_" . $pay_id . "_" . $type,
            'total_fee'     => 1,
            'notify_url'    => env('APP_URL') . '/hotel/pay/notify/' . $wechat->appid, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'    => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'        => $this->openid,
        ]);

        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS'){
            return response()->json(['code'=>500,'msg'=>'订单失败：'.$result['return_msg']]);
        }else{
            $jssdk = $app->jssdk;
            $config = $jssdk->bridgeConfig($result['prepay_id'], false);
            $config['out_trade_no'] = $book->code . "_" . $pay_id . "_" . $type;
            $config['pay_status']   = 0;

            return response()->json([
                'code'  => 200,
                'msg'   => '待支付',
                'data'  => $config
            ]);
        }
    }

    /**
     * 支付成功
     */
    public function paySuccess(Request $request){
        $out_trade_no = trim($request->input('out_trade_no'));
        $arr    = explode("_", $out_trade_no);
        $code   = $arr[0];
        $pay_id = intval($arr[1]);

        $type = intval($arr[2]);

        $pay_setting = PaySetting::where(['company_id'=>$this->company_id, 'is_active'=>1])->first();
        $wechat = Wechat::where(['company_id'=>$this->company_id, 'type'=>2, 'is_active'=>1])->first();

        $data = base64_decode($pay_setting->content, true);
        $pay_setting = unserialize($data);

        $config = [
            'app_id'        => $wechat->appid,
            'mch_id'        => $pay_setting['mch_id'],
            'key'           => $pay_setting['key'],   // API 密钥
            'notify_url'    => env('APP_URL') . '/hotel/pay/notify',
        ];

        $app = Factory::payment($config);
        $res = $app->order->queryByOutTradeNumber($out_trade_no);

        Log::info("pay:" . json_encode($res));

        if(isset($res['trade_state']) && strtoupper($res['trade_state']) == 'SUCCESS'){
            $book = Book::where(['company_id'=>$this->company_id, 'is_active'=>1, 'code'=>$code])->first();

            $hotel = Hotel::find($book->hotel_id);

            BookPay::where(['id'=>$pay_id])->update(['pay_status'=>1, 'pay_at'=>time(), 'pay_method'=>1, 'handover_id'=>$hotel->handover_id]);

            if($type == 2){
                $relation = BookRelation::find($book->relation_id);
                $book_ids = explode(",", $relation->book_ids);
            }else{
                $book_ids = [$book->id];
            }

            foreach ($book_ids as $book_id){
                $objBook = Book::where(['company_id'=>$this->company_id, 'is_active'=>1])->find($book_id);
                if(date('Y-m-d', $objBook->start_at) == date('Y-m-d')){
                    $objBook->status = 3;
                }else{
                    $objBook->status = 2;
                }
                $objBook->pay_status = 1;
                $objBook->save();

                /**
                 * 扣除积分
                 */
                if($objBook->score > 0){
                    Log::info("pay-2：" . json_encode($objBook));
                    ConsumeScore::dispatch($objBook);
                }
            }
            BookTrade::where(['pay_id'=>$pay_id, 'is_active'=>1])->whereIn('book_id', $book_ids)->update(['pay_status'=>1, 'pay_at'=>time(), 'handover_id'=>$hotel->handover_id]);

            return response()->json([
                'code'  => 200,
                'msg'   => '支付成功'
            ]);
        }else{
            return response()->json([
                'code'  => 500,
                'msg'   => '支付失败'
            ]);
        }
    }

    /**
     * 积分兑换
     */
    public function score(Request $request){
        $max_score = intval($request->input('max_score'));
        $user = User::find($this->user_id);
        $user_score = $user->score;

        $score = min($max_score, $user_score);

        $setting = Setting::where(['company_id'=>$this->company_id, 'key'=>'user_socre_money', 'is_active'=>1])->first();
        $user_socre_money = intval($setting->value);

        $ret_score = $user_socre_money ? floor($score/$user_socre_money) * $user_socre_money : 0;

        return response()->json([
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => [
                'score' => $ret_score,
                'step'  => $user_socre_money,
            ]
        ]);
    }

}
