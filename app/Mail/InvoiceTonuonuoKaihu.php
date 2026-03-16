<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Hotel\InvoiceRegister;

class InvoiceTonuonuoKaihu extends Mailable
{
    use Queueable, SerializesModels;

    public $info;

    // 创建一个新消息实例。
    public function __construct(InvoiceRegister $info)
    {
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('融宝科技 商户数电发票开户')
            ->from('3664839@qq.com','融宝科技')
            ->view('emails.invoice-to-nuonuo-kaihu');

        /*->with([
        'orderName' => $this->order->name,
        'orderPrice' => $this->order->price,
    ]); // 一旦数据已经用 with 方法传递，它们将自动在视图中加载 */
         //->attach('/path/to/file'); // 附件
            //->text('emails.orders.shipped_plain'); //纯文本邮件
    }
}
