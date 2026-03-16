<?php

namespace App\Admin\Metrics\Admin;

use Dcat\Admin\Widgets\Metrics\Card;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\User;
class HotelTotal extends Card
{
    /**
     * 卡片底部内容.
     *
     * @var string|Renderable|\Closure
     */
    protected $footer;

    /**
     * 初始化卡片.
     */
    protected function init()
    {
        parent::init();
        // $this->subTitle('最近30天');
        //$this->title('酒店总数');
        /*$this->dropdown([
            '7' => 'Last 7 Days',
            '28' => 'Last 28 Days',
            '30' => 'Last Month',
            '365' => 'Last Year',
        ]);*/
        $type = 0;
        switch ($this->title) {
            case '酒店总数' :
                $type = 1;
                break;
            case '预订单量':
                $type = 2;
                break;
            case '预订交易额':
                $type = 3;
                break;
            case '总会员数':
                $type = 4;
                break;
            case '扫码购订单':
                $type = 5;
                break;
            case '扫码购总交易':
                $type = 6;
                break;
            default:
                break;
        }
        $this->data = [
            'type' => $type,
        ];
    }
    // 传递自定义参数到 handle 方法
    public function parameters() : array
    {
        return $this->data;
    }

    /**
     * 处理请求.
     *
     * @param Request $request
     *
     * @return void
     */
    public function handle(Request $request)
    {
        $type = $request->get('type');
        $total_num = 0;
        switch ($type) {
            case 1 :
                $total_num = Hotel::where([['id','<>',1],['shop_open','=',1]])->count();
                break;
            case 2 :
                $total_num = BookingOrder::where(['pay_status'=> 1])->count();
                break;
            case 3 :
                $total_num = BookingOrder::where(['pay_status'=> 1])->sum('total_cost');
                break;
            case 4 :
                $total_num = 10;//User::where([['card_code','<>','']])->count();
                break;
            case 5 :
                $total_num = 0;
                break;
            case 6 :
                $total_num = 0;
                break;
            default:
                $total_num = 0;
                break;
        }

        switch ($request->get('option')) {
            case '365':
                $this->content(mt_rand(600, 1500));
                $this->down(mt_rand(1, 30));
                break;
            case '30':
                $this->content(mt_rand(170, 250));
                $this->up(mt_rand(12, 50));
                break;
            case '28':
                $this->content(mt_rand(155, 200));
                $this->up(mt_rand(5, 50));
                break;
            case '7':
            default:
                $this->content($total_num);
                $this->up(15);
        }
    }

    /**
     * @param int $percent
     *
     * @return $this
     */
    public function up($percent)
    {
        return $this->footer(
            "<i class=\"feather icon-trending-up text-success\"></i> {$percent}% Increase"
        );
    }

    /**
     * @param int $percent
     *
     * @return $this
     */
    public function down($percent)
    {
        return $this->footer(
            "<i class=\"feather icon-trending-down text-danger\"></i> {$percent}% Decrease"
        );
    }

    /**
     * 设置卡片底部内容.
     *
     * @param string|Renderable|\Closure $footer
     *
     * @return $this
     */
    public function footer($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * 渲染卡片内容.
     *
     * @return string
     */
    public function renderContent()
    {
        $content = parent::renderContent();

        return <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">{$content}</h2>
</div>
<div class="ml-1 mt-1 font-weight-bold text-80">
    {$this->renderFooter()}
</div>
HTML;
    }

    /**
     * 渲染卡片底部内容.
     *
     * @return string
     */
    public function renderFooter()
    {
        // return $this->toString($this->footer);
    }
}
