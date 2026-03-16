<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Seller;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Illuminate\Http\Request;

class SellerController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Seller(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('type');
            $grid->column('user_id');
            $grid->column('owner');
            $grid->column('name');
            $grid->column('star');
            $grid->column('address');
            $grid->column('link_name');
            $grid->column('link_tel');
            $grid->column('tel');
            $grid->column('handle');
            $grid->column('open_time');
            $grid->column('wake');
            $grid->column('wifi');
            $grid->column('park');
            $grid->column('breakfast');
            $grid->column('unionPay');
            $grid->column('gym');
            $grid->column('boardroom');
            $grid->column('luggage');
            $grid->column('water');
            $grid->column('policy');
            $grid->column('swim');
            $grid->column('airport');
            $grid->column('introduction');
            $grid->column('img');
            $grid->column('rule');
            $grid->column('prompt');
            $grid->column('scort');
            $grid->column('bq_logo');
            $grid->column('support');
            $grid->column('ewm_logo');
            $grid->column('time');
            $grid->column('areaid');
            $grid->column('coordinates');
            $grid->column('sfz_img1');
            $grid->column('sfz_img2');
            $grid->column('yy_img');
            $grid->column('ts_img');
            $grid->column('other');
            $grid->column('zd_money');
            $grid->column('state');
            $grid->column('sq_time');
            $grid->column('uniacid');
            $grid->column('is_use');
            $grid->column('ll_num');
            $grid->column('bd_id');
            $grid->column('ye_open');
            $grid->column('wx_open');
            $grid->column('dd_open');
            $grid->column('room_num');
            $grid->column('is_pay_sms');
            $grid->column('pet');
            $grid->column('card_support');
            $grid->column('card_type');
            $grid->column('otherpay_support');
            $grid->column('otherpay_type');
            $grid->column('arrival_departure_time');
            $grid->column('decorate_time');
            $grid->column('email');
            $grid->column('offsite_facilities');
            $grid->column('service_facility');
            $grid->column('store_type');
            $grid->column('store_brand');
            $grid->column('breakfast_amount');
            $grid->column('is_tianze');
            $grid->column('non_cancelling_time');
            $grid->column('non_cancelling_explain');
            $grid->column('activity_conetnt');
            $grid->column('collection_status');
            $grid->column('share_status');
            $grid->column('is_refund_sms');
            $grid->column('equity_card_status');
            $grid->column('update_time');
            $grid->column('fundauth_status');
            $grid->column('send_sms_tel');
            $grid->column('my_app_description');
            $grid->column('seller_code');
            $grid->column('food_status');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
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
        return Show::make($id, new Seller(), function (Show $show) {
            $show->field('id');
            $show->field('type');
            $show->field('user_id');
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
        return Form::make(new Seller(), function (Form $form) {
            $form->display('id');
            $form->text('type');
            $form->text('user_id');
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
