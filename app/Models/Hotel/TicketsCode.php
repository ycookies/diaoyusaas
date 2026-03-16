<?php

namespace App\Models\Hotel;

use App\Models\Hotel\Order\Order;

class TicketsCode extends HotelBaseModel {

    protected $table = 'tickets_code';

    protected $guarded = [];
    protected $appends = ['ticket_code_qrcode'];
    //protected $fillable = ['id','hotel_id', 'order_no', 'ticket_code', 'status'];

    public function getTicketCodeQrcodeAttribute(){
        return env('APP_URL') . '/hotel/mintools/getQrcode?qrcode_con=' . $this->attributes['ticket_code'] . '&hotel_id=' . $this->attributes['hotel_id'];
    }

    // 外部生成码后保存
    public static function waibuAddCode($hotel_id,$sign,$order_no,$ticket_code){
        self::create(['hotel_id' => $hotel_id,'sign'=>$sign,'order_no' => $order_no, 'ticket_code' => $ticket_code, 'status' => 0]);
        return true;
    }

    /**
     * @desc 获取 核销码
     * @param $code_prefix 业务前缀
     * author eRic
     * dateTime 2025-02-15 17:35
     */
    public static function getOnlyCode($code_prefix){
        $max_attempts = 10; // 最大尝试次数
        $attempts = 0;
        do {
            $ticket_code = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $ticket_code = $ticket_code.$code_prefix;

            $attempts++;
        } while (self::where('ticket_code', $ticket_code)->exists() && $attempts <= $max_attempts);
        return $ticket_code;
    }

    // 根据订单生成核销码(一个，或多个)
    public static function generateCode($order_no) {
        $order_info = Order::with(['detail'])->where(['order_no' => $order_no])->first();

        // 检查是否已经创建过
        $counts = self::where('order_no', $order_no)->count();
        if($counts > 0){
            return false;
        }
        // 业务核销码前缀
        $hexiaocode_prefix = !empty(Order::Sign_hexiaocode_prefix[$order_info->sign]) ? Order::Sign_hexiaocode_prefix[$order_info->sign]:'';
        if ($order_info->is_use_verify == 1) {
            // 生成几个核销码
            $verify_code_num = $order_info->detail->num;
            for ($i = 0; $i < $verify_code_num; $i++) {
                $ticket_code = self::getOnlyCode($hexiaocode_prefix);
                self::create(['hotel_id' => $order_info->hotel_id,'sign'=>$order_info->sign,'order_no' => $order_no, 'ticket_code' => $ticket_code, 'status' => 0]);
            }
        }
        return true;
    }

    /**
     * @desc 订单核销
     * @param $order_no 订单号
     * @param $ticket_code 核销码
     * @param $verifier_id 核销员ID
     * @param $device_info 设备类型
     * @param $remark 备注
     * @return bool
     * author eRic
     * dateTime 2025-02-13 19:29
     */
    public static function verify($order_no, $ticket_code, $verifier_id, $device_info,$remark = '') {
        $code_info = self::where(['order_no' => $order_no, 'ticket_code' => $ticket_code])->first();
        if ($code_info->status == 1) {
            return true;
        }
        $code_info->status = 1;
        $code_info->save();
        $insdata = [
            'hotel_id'    => $code_info->hotel_id,
            'ticket_id'   => $code_info->id,
            'verifier_id' => $verifier_id, // 核销员ID
            'verified_at' => date('Y-m-d H:i:s'),
            'device_info' => $device_info, // 核销设备信息,
            'verified_remark' => $remark,
        ];
        TicketsVerificationRecord::firstOrCreate(['ticket_id' => $code_info->id], $insdata);

        // 更新订单状态


        return true;
    }

    /**
     * @desc 获取订单的全部核销码
     * @param $order_no
     * author eRic
     * dateTime 2025-02-15 18:37
     */
    public static function getOrderAllHexiaoCode($order_no){
       $list = self::where(['order_no' => $order_no])->get();
       $code_arr = [];
        foreach ($list as $item) {
            $code_arr[] = $item['ticket_code'];
       }
       return implode(',',$code_arr);
    }
}
