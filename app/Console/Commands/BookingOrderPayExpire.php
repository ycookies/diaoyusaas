<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\RoomBookingLog;
class BookingOrderPayExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:bookingOrderPayExpire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '酒店预订未支付过期';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 限定10分钟
        $dangqiantime = date('Y-m-d H:i:s');

        // 开始记录命令输出到日志
        ob_start();

        //查询超过时间未支付的订单 自动关闭
        $where = [
            ['status','=',1],
            ['pay_expire_time','<=',$dangqiantime]
        ];
        $list = BookingOrder::where($where)->get();
        if ($list->isEmpty()) {
            info('没有过期未支付订单');
            die('没有过期未支付订单');
        }
        $this->info('有'.count($list).'个过期未支付订单待处理');

        $order_no_arr = [];
        foreach ($list as $key => $item) {
            $updata = [
                'status'=> 3, // 已取消
            ];
            BookingOrder::where(['id'=> $item->id])->update($updata);
            // 如果有使用优惠券，把优惠券返还
            if(!empty($item->coupons_id)){
                \App\Services\CouponService::fanhui($item->user_id,json_decode($item->coupons_id,true));
            }
            $order_no_arr[] = $item->out_trade_no;
            // 清除预订记录
            RoomBookingLog::dellog($item);
        }
        $msgs = '已处理完成过期未支付订单：'.implode(',',$order_no_arr).'';
        $this->info($msgs);
        // 结束记录命令输出到日志
        $output = ob_get_clean();
        info('[order:bookingOrderPayExpire]'.$output);
        return $msgs;
    }
}
