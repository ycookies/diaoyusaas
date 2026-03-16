<?php

namespace App\Admin\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Admin\Repositories\HotelSettingRep;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Hotel;
class HotelSettingForm extends Form implements LazyRenderable
{
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input)
    {
        return $this->success('保存成功');
    }

    public function default()
    {
        /**
         * [
        'trade_fee_author',
        'profitsharing_type',
        'profitsharing_author',
        'profitsharing_author_platform_price',
        'profitsharing_author_agent_price',
        'profitsharing_author_businessBD_price',
        'profitsharing_author_cooperate_price',
        'profitsharing_author_platform_ratio',
        'profitsharing_author_agent_ratio',
        'profitsharing_author_businessBD_ratio',
        'profitsharing_author_cooperate_ratio',
        ]
         */
        $data = HotelSetting::getlists([
            'is_booking_profitsharing',
            'profitsharing_author_platform_ratio',
            'profitsharing_type',
            'profitsharing_author_cooperate_ratio_1',
            'profitsharing_author_cooperate_ratio_2'
        ],$this->payload['hotel_id']);
        return $data;
    }
    public function form()
    {
        $this->confirm('确认已经填写完整了吗？');
        $this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        //$form1->html('<h3>分账设置</h3>');
        $hotel_name = Hotel::where(['id'=> $this->payload['hotel_id']])->value('name');
        $this->html('查看服务商如何设置分账:<a href="https://pay.weixin.qq.com/wiki/doc/api/allocation_sl.php?chapter=24_2&index=2" target="_blank"> >>> 查看</a> <br/> <span class="text-warning"> 必须设置好后，才能开启分账。否则交易时会提示</span> <span class="text-danger">没有分账权限</span>');
        $this->display('hotel_name','酒店名')->value($hotel_name);
        $this->hidden('hotel_id')->value($this->payload['hotel_id']);
        $this->hidden('action_name')->value('profitsharing');
        /*$this->radio('trade_fee_author','交易手续费出资方')
            ->options(['seller'=>'商家','platform'=> '平台'])
            ->value('seller')->help('例如:交易1000元 微信支付0.6%手续费,平台固定金额分账60元 <br/>1.平台出资: 商家最终收到款项 940元,平台最终得54元<br/>2.商家出资: 商家最终收到款项 934元，平台最终得60元(1000需减去60+6)<br/>')->required();
        $this->html('<span class="text-danger"> (合作方,推广人,代理商) 都是在平台分账所得内 再次分账</span>');
        */
        //$this->html('<span class="text-danger"> 交易手续费由商家承担</span>');
        /*$this->divider('业务分账开关');
        $this->radio('is_tuangou_profitsharing', '团购')
            ->options(['0'=> '关闭','1'=>'开启'])->required();
        $this->radio('is_booking_profitsharing', '酒店订房')
            ->options(['0'=> '关闭','1'=>'开启'])
            ->required();*/
        /* ->when('0', function (Form $form) {

         })
         ->when('1', function (Form $form) {
             $form->rate('profitsharing_author_platform_ratio', '平台分账比率')->width(6)->help('例：交易金额:1000，扣出手续费（0.6%）:994 平台分账5%, 分得49.7元');

             $form->radio('profitsharing_type','合作方分账方式')
             ->when(1, function (Form $form) {
                 $form->rate('profitsharing_author_cooperate_ratio_1', '合作方分账比率')->width(6)->help('基于平台分账所得 在平台分的金额里面，分账比率10%, 分得4.97元(49.7*0.10=4.97)');
             })
             ->when(2, function (Form $form) {
                 $form->rate('profitsharing_author_cooperate_ratio_2', '合作方分账比率')->width(6)->help('基于交易金额,分账比率1%, 分得9.94元(994*0.01=9.94)');

             })
             ->options(['1'=> '基于平台分账所得','2'=> '基于交易金额']);
         });*/
        $this->divider('分账比率配置');
        $this->rate('profitsharing_author_platform_ratio', '平台分账比率')
            ->width(6)->help('例：交易金额:1000，扣出手续费（0.6%）:994 平台分账5%, 分得49.7元')->required();;

        $this->radio('profitsharing_type','合作方分账方式')
            ->when(1, function (Form $form) {
                $form->rate('profitsharing_author_cooperate_ratio_1', '合作方分账比率')
                    ->width(6)
                    ->help('基于平台分账所得 在平台分的金额里面，分账比率10%, 分得4.97元(49.7*0.10=4.97)');
            })
            ->when(2, function (Form $form) {
                $form->rate('profitsharing_author_cooperate_ratio_2', '合作方分账比率')
                    ->width(6)
                    ->help('基于交易金额,分账比率1%, 分得9.94元(994*0.01=9.94)');

            })
            ->options(['1'=> '基于平台分账所得','2'=> '基于交易金额'])->required();
        

        //$this->rate('profitsharing_author_agent_ratio', '合作方分账比率')->width(6)->help('在平台分的金额里面，分账比率15%, 分得7.5元');

        /*$this->radio('profitsharing_type','分账方式')->when(1, function (Form $form) {
            // 值为1和4时显示文本框
            $form->number('profitsharing_author_platform_price', '平台分账金额')->width(6)->help('固定金额 例：收款1000，平台分账60元');
            $form->number('profitsharing_author_agent_price', '合作方分账金额')->width(6)->help('在平台分的金额里面，固定金额10元，分账10元');
            $form->number('profitsharing_author_businessBD_price', '推广人分账金额')->width(6)->help('在平台分的金额里面，固定金额5元 分账5元');
            $form->number('profitsharing_author_cooperate_price', '代理商分账金额')->width(6)->help('在平台分的金额里面，固定金额5元 分账5元');

        })->when(2, function (Form $form) {
            // 值为1和4时显示文本框
            $form->rate('profitsharing_author_platform_ratio', '平台分账比率')->width(6)->help('比率分账 例：收款1000，平台分账5%, 分得50元');
            $form->rate('profitsharing_author_agent_ratio', '合作方分账比率')->width(6)->help('在平台分的金额里面，分账比率15%, 分得7.5元');
            $form->rate('profitsharing_author_businessBD_ratio', '推广人分账比率')->width(6)->help('在平台分的金额里面，分账比率10%, 分得5元');
            $form->rate('profitsharing_author_cooperate_ratio', '代理商分账比率')->width(6)->help('在平台分的金额里面，分账比率10%, 分得5元');

        })->options([
            1 => '固定金额',
            2 => '百分比率',
        ])->required();

        $this->checkbox('profitsharing_author','分账参于者')
            ->options(['agent'=> '代理商','cooperate'=> '合作方','business_BD'=>'推广人'])
            ->help('只有勾选者 才参于分账')->required();*/
        $this->disableResetButton();
    }
}
