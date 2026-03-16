<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\YxSeller;
use App\Admin\Repositories\HotelSettingRep;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\HotelSettingForm;
use Dcat\Admin\Widgets\Alert;
use App\User;
use App\Models\Hotel\BookingOrder;

class YxSellerController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->header('酒店列表')
            ->description('全部')
            ->breadcrumb(['text'=>'酒店列表','uri'=>''])
            ->body($this->grid());
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid =  Grid::make(YxSeller::with('user'), function (Grid $grid) {
            $grid->model()->where([['id','<>',1],['shop_open','=',1]])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('user.name','商家')->display(function($hotel_user_id){
                return "<a target='_blank' href='".url('admin/merchant-users?_search_='.$hotel_user_id)."' >".$hotel_user_id."</a>";
            });
            $grid->column('ewm_logo','酒店Logo')->image('','100');
            $grid->column('name','酒店名称');
            $grid->column('total_user_num','会员总数')->display(function(){
                return User::where(['hotel_id'=> $this->id])->count();
            });
            $grid->column('total_order_num','订单总量')->help('客房预定')->display(function(){
                return BookingOrder::where(['hotel_id'=> $this->id,'pay_status'=>1])->count();
            });
            $grid->column('total_deal_money','总成交金额')->help('客房预定')->display(function(){
                return BookingOrder::where(['hotel_id'=> $this->id,'pay_status'=>1])->sum('price');
            });
            //$grid->column('type');
            //$grid->column('owner');
            /*$grid->column('hotel_info','酒店信息')->display(function ($hotel_info){
                $htmls = $this->name.'<br/>';
                $htmls .= $this->star.'<br/>';
                $htmls .= $this->address.'<br/>';
                $htmls .= $this->link_name.'<br/>';
                $htmls .= $this->link_tel.'<br/>';
                $htmls .= $this->tel.'<br/>';
                $htmls .= $this->open_time.'<br/>';
                return $htmls;
            });*/
            /*$grid->column('name','酒店名');
            $grid->column('star');
            $grid->column('address','地址');
            $grid->column('link_name');
            $grid->column('link_tel');
            $grid->column('tel');*/
            //$grid->column('handle');
            //$grid->column('open_time');
            /**/
            //$grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->quickSearch(['name'])->placeholder('酒店名称');
            $grid->disableFilterButton();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                //
                $actions->disableView();
                $actions->prepend('<a href="'.admin_url('/hotelinfo?hotel_id='.$actions->row->id).'">查看基础信息</a>');
                //$form1->disableSubmitButton();
                //$card = Card::make('', $form1);
                /*$modal = Modal::make()
                    ->lg()
                    ->title('配置分账参数')
                    ->body(HotelSettingForm::make()->payload($actions->row->toArray()))
                    ->button('<i class="feather icon-shuffle tips" data-title="配置分账参数"></i>');
                $actions->append($modal);*/
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->like('name','酒店名称')->width(3);
        
            });
        });
        $htmls = <<<HTML
        <ul><li>这里展示的各酒店的统计数据</li></ul>
HTML;

        $alert = Alert::make($htmls,'提示')->info();
        return $alert.$grid;
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
        return Show::make($id, new YxSeller(), function (Show $show) {
            $show->field('id');
            $show->field('type');
            $show->field('hotel_user_id');
            $show->field('owner');
            $show->field('name');
            $show->field('star');
            $show->field('address');
            $show->field('link_name');
            $show->field('link_tel');
            $show->field('tel');
            $show->field('handle');
            $show->field('open_time');
            $show->field('wake');
            $show->field('wifi');
            $show->field('park');
            $show->field('breakfast');
            $show->field('unionPay');
            $show->field('gym');
            $show->field('boardroom');
            $show->field('luggage');
            $show->field('water');
            $show->field('policy');
            $show->field('swim');
            $show->field('airport');
            $show->field('introduction');
            $show->field('img');
            $show->field('rule');
            $show->field('prompt');
            $show->field('scort');
            $show->field('bq_logo');
            $show->field('support');
            $show->field('ewm_logo');
            $show->field('time');
            $show->field('areaid');
            $show->field('coordinates');
            $show->field('sfz_img1');
            $show->field('sfz_img2');
            $show->field('yy_img');
            $show->field('ts_img');
            $show->field('other');
            $show->field('zd_money');
            $show->field('state');
            $show->field('sq_time');
            $show->field('uniacid');
            $show->field('is_use');
            $show->field('ll_num');
            $show->field('bd_id');
            $show->field('ye_open');
            $show->field('wx_open');
            $show->field('dd_open');
            $show->field('room_num');
            $show->field('is_pay_sms');
            $show->field('pet');
            $show->field('card_support');
            $show->field('card_type');
            $show->field('otherpay_support');
            $show->field('otherpay_type');
            $show->field('arrival_departure_time');
            $show->field('decorate_time');
            $show->field('email');
            $show->field('offsite_facilities');
            $show->field('service_facility');
            $show->field('store_type');
            $show->field('store_brand');
            $show->field('breakfast_amount');
            $show->field('is_tianze');
            $show->field('non_cancelling_time');
            $show->field('non_cancelling_explain');
            $show->field('activity_conetnt');
            $show->field('collection_status');
            $show->field('share_status');
            $show->field('is_refund_sms');
            $show->field('equity_card_status');
            $show->field('update_time');
            $show->field('fundauth_status');
            $show->field('send_sms_tel');
            $show->field('my_app_description');
            $show->field('seller_code');
            $show->field('food_status');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new YxSeller(), function (Form $form) {
            $form->display('id');
            $form->text('type');
            $form->text('hotel_user_id');
            $form->text('owner');
            $form->text('name');
            $form->text('star');
            $form->text('address');
            $form->text('link_name');
            $form->text('link_tel');
            $form->text('tel');
            $form->text('handle');
            $form->text('open_time');
            $form->text('wake');
            $form->text('wifi');
            $form->text('park');
            $form->text('breakfast');
            $form->text('unionPay');
            $form->text('gym');
            $form->text('boardroom');
            $form->text('luggage');
            $form->text('water');
            $form->text('policy');
            $form->text('swim');
            $form->text('airport');
            $form->text('introduction');
            $form->text('img');
            $form->text('rule');
            $form->text('prompt');
            $form->text('scort');
            $form->text('bq_logo');
            $form->text('support');
            $form->text('ewm_logo');
            $form->text('time');
            $form->text('areaid');
            $form->text('coordinates');
            $form->text('sfz_img1');
            $form->text('sfz_img2');
            $form->text('yy_img');
            $form->mediaSelector('imageskk', '系列图片')
                ->options(['length' => 10,'type' => 'image'])
                ->help('上传或选择10个图片');
            $form->text('ts_img');
            $form->text('other');
            $form->text('zd_money');
            $form->text('state');
            $form->text('sq_time');
            $form->text('uniacid');
            $form->text('is_use');
            $form->text('ll_num');
            $form->text('bd_id');
            $form->text('ye_open');
            $form->text('wx_open');
            $form->text('dd_open');
            $form->text('room_num');
            $form->text('is_pay_sms');
            $form->text('pet');
            $form->text('card_support');
            $form->text('card_type');
            $form->text('otherpay_support');
            $form->text('otherpay_type');
            $form->text('arrival_departure_time');
            $form->text('decorate_time');
            $form->text('email');
            $form->text('offsite_facilities');
            $form->text('service_facility');
            $form->text('store_type');
            $form->text('store_brand');
            $form->text('breakfast_amount');
            $form->text('is_tianze');
            $form->text('non_cancelling_time');
            $form->text('non_cancelling_explain');
            $form->text('activity_conetnt');
            $form->text('collection_status');
            $form->text('share_status');
            $form->text('is_refund_sms');
            $form->text('equity_card_status');
            $form->text('update_time');
            $form->text('fundauth_status');
            $form->text('send_sms_tel');
            $form->text('my_app_description');
            $form->text('seller_code');
            $form->text('food_status');
        });
    }
}
