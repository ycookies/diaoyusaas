<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /*// 创建一个新消息实例。
    public function __construct(Order $order)
    {
        $this->order = $order;
    }*/

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('预定客房成功')
            ->from('3664839@qq.com','融宝易住')
            ->view('emails.orders.booking_room');
        /*->with([
        'orderName' => $this->order->name,
        'orderPrice' => $this->order->price,
    ]); // 一旦数据已经用 with 方法传递，它们将自动在视图中加载 */
         //->attach('/path/to/file'); // 附件
            //->text('emails.orders.shipped_plain'); //纯文本邮件
    }
}
