<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers\Extend;

use App\Models\Hotel\TradeOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Modal;
use App\Services\WxPayService;
// 列表
class TradeOrderController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('普通收款订单')
            ->description('全部')
            ->breadcrumb(['text'=>'普通收款','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(TradeOrder::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'type'=> 101])->orderBy('id', 'DESC');
            $grid->column('id');
            $grid->column('user.nick_name','用户');
            $grid->column('out_trade_no');
            /*$grid->column('trade_no');
            $grid->column('type');
            $grid->column('fina_type');
            $grid->column('PayWays');*/
            $grid->column('total_amount');
            /*$grid->column('real_amount');
            $grid->column('rate');
            $grid->column('rate_amount');
            $grid->column('hb_fq_num');
            $grid->column('hb_fq_seller_percent');
            $grid->column('hb_fq_sxf');
            $grid->column('buyer_id');
            $grid->column('status');
            $grid->column('remark');

            $grid->column('cost_rate');
            $grid->column('service_rate');*/
            $grid->column('pay_status')->using(TradeOrder::$pay_status_arr)->label(TradeOrder::$pay_status_label);
            $grid->column('created_at');
            $grid->quickSearch(['out_trade_no'])->placeholder('订单号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->disableFilterButton();
            $qrcode  = WxPayService::makeTradeQrcode(Admin::user()->hotel_id);
            $modal = Modal::make()
                ->title('消费收款二维码')
                ->body('<div style="text-align: center"><img style="margin: 100px auto;" src="'.$qrcode.'" width="200" /></div>')
                ->button('<button class="btn btn-white btn-outline"><i class="fa fa-qrcode"></i> 小程序收款二维码</button>');
            $grid->tools($modal);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('out_trade_no','订单号');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new TradeOrder(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('user_id');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('type');
            $show->field('fina_type');
            $show->field('PayWays');
            $show->field('total_amount');
            $show->field('real_amount');
            $show->field('rate');
            $show->field('rate_amount');
            $show->field('hb_fq_num');
            $show->field('hb_fq_seller_percent');
            $show->field('hb_fq_sxf');
            $show->field('buyer_id');
            $show->field('status');
            $show->field('remark');
            $show->field('pay_status');
            $show->field('cost_rate');
            $show->field('service_rate');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TradeOrder(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('user_id');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('type');
            $form->text('fina_type');
            $form->text('PayWays');
            $form->text('total_amount');
            $form->text('real_amount');
            $form->text('rate');
            $form->text('rate_amount');
            $form->text('hb_fq_num');
            $form->text('hb_fq_seller_percent');
            $form->text('hb_fq_sxf');
            $form->text('buyer_id');
            $form->text('status');
            $form->text('remark');
            $form->text('pay_status');
            $form->text('cost_rate');
            $form->text('service_rate');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
