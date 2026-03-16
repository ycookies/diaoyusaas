<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hotel\Usercoupon;
use App\Models\Hotel\RoomBookingLog;
class CouponExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理优惠券过期';

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
            ['coupon_status','=',0],
            ['expire_time','<=',$dangqiantime]
        ];
        $list = Usercoupon::where($where)->get();
        if ($list->isEmpty()) {
            info('没有过期优惠券处理');
            die('没有过期优惠券处理');
        }
        $this->info('有'.count($list).'个过期优惠券待处理');

        $order_no_arr = [];
        foreach ($list as $key => $item) {
            $updata = [
                'coupon_status'=> 2, // 已取消
            ];
            Usercoupon::where(['id'=> $item->id])->update($updata);
            $order_no_arr[] = $item->id;
        }
        $msgs = '已处理过期优惠券：'.implode(',',$order_no_arr).'';
        $this->info($msgs);
        // 结束记录命令输出到日志
        $output = ob_get_clean();
        info('[coupon:expire]'.$output);
        return $msgs;
    }
}
