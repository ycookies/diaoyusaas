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

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Hotel\WxopenMiniProgramOauth;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;

use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Hotel\HotelSetting;
use App\Merchant\Repositories\ParkingCarInTable;
use App\Merchant\Repositories\ParkingCarOutTable;
use App\Merchant\Repositories\ParkingChargeInfoTable;
use App\Merchant\Repositories\ParkingCarOutOrderTable; //
use App\Services\ParkingService;

// 停车场
class ParkingController extends AdminController
{

    public $oauth;
    public function index(Content $content) {
        return $content
            ->header('停车场管理')
            ->description('酒店智慧停车场管理')
            ->breadcrumb(['text' => '智慧停车场', 'uri' => ''])
            ->row(function(Row $row) {
                $row->column(2,  $this->cbox());

                $row->column(10, $this->pageMain());
            });
    }

    public function cbox($sc_id = 1){
        $nav_menu = [
            '1' => '基本信息',
            '2' => '[缴费/免费]放行',
            '3' => '入场记录',
            '4' => '出场记录',
            '5' => '收费明细',
            //'5' => '模板消息',
            //'6' => '粉丝列表',
        ];
        $datas = Request()->all();
        $hangzu_id = !empty($datas['hangzu_id'])? $datas['hangzu_id']:'';
        $hangzulist  = $nav_menu;
        $hzhtml = "<ul class='list-group list-group-flush'>";
        foreach ($hangzulist as $key => $items){
            $class = '';
            if(!empty($hangzu_id)){
                if($key == $hangzu_id){
                    $class = 'class="text-danger"';
                }
            }else{
                if($key == 1){
                    $class = 'class="text-danger"';
                }
            }

            $hzhtml .= '<li class="list-group-item"><a '.$class.' href="/merchant/extend/parking?&hangzu_id='.$key.'">'.$items.'</a></li>';
        }
        //$hzhtml .= '<li class="list-group-item"><a href="/merchant/wxgzh?&sc_id='.$sc_id.'" target="_blank">水电费</a></li>';
        $hzhtml .= '</ul>';
        $box = new Box('操作项', $hzhtml);
        //$box->collapsable();
        return $box;
    }

    // 页面
    public function pageMain() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'wxgzh'])->first();
        $this->oauth = $oauth;
        $is_open_parking = 1;
        $data = [];
        $tab  = Tab::make();
        $datas = Request()->all();
        $hangzu_id = !empty($datas['hangzu_id'])? $datas['hangzu_id']:'';
        if(!empty($is_open_parking)){
            if($hangzu_id == 1 || $hangzu_id == ''){
                $tab->add('基本信息', $this->tab1());
            }
            if($hangzu_id == 2){
                $tab->add('[缴费/免费]放行', $this->tab2());
            }
            if($hangzu_id == 3){
                $tab->add('入场记录', $this->tab3());
            }
            if($hangzu_id == 4){
                $tab->add('出场记录', $this->tab4());
            }
            if($hangzu_id == 5){
                $tab->add('收费明细', $this->tab5());
            }
            /*if($hangzu_id == 5){
                $tab->add('模板消息', $this->tab5());
            }
            if($hangzu_id == 6){
                $tab->add('粉丝列表', $this->tab6());
            }*/
        }else{
            $tab->add('基本信息', $this->tab1());
        }

        return $tab->withCard();
    }
    // 基本信息
    public function tab1(){
        $flds     = [
            'parkingNo',
        ];

        $qrcode  = ParkingService::makeParkingQrcode(Admin::user()->hotel_id);

        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('parking_config');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        if($qrcode !== false){
            $form->html('<div><img src="'.$qrcode.'" width="140" /></div>')->label('停车缴费二维码');
        }

        $form->text('parkingNo','停车场编号')
            ->help('<span class="text-success">联系平台获取</span>')
            ->placeholder('例:P1715177379306')
            ->required();

        $form->disableResetButton();
        $card =  Card::make('停车场基本配置',$form);
        return $card;
    }

    public function tab2(){
        $grid = Grid::make(new ParkingCarOutOrderTable(), function (Grid $grid) {
            //$grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('carNo','车牌号码');
            $grid->column('createTime','入场时间')->display(function ($e){
                $seconds = bcdiv($this->createTime, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->disableFilterButton();
            $grid->disableRowSelector();
            $grid->disableColumnSelector();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->quickSearch(['carNo'])->placeholder('查询车牌号码 停车费用');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('carNo','车牌号码');

            });
        });
        $card1 = Card::make('', $grid);
        return $card1;
    }

    public function tab3(){
        $grid = Grid::make(new ParkingCarInTable(), function (Grid $grid) {
            //$grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('carNo','车牌号码');
            $grid->column('createTime','入场时间')->display(function ($e){
                $seconds = bcdiv($this->createTime, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->disableFilterButton();
            $grid->disableRowSelector();
            $grid->disableColumnSelector();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->quickSearch(['carNo'])->placeholder('车牌号码');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('carNo','车牌号码');

            });
        });
        $card1 = Card::make('', $grid);
        return $card1;
    }
    public function tab4(){
        $grid = Grid::make(new ParkingCarOutTable(), function (Grid $grid) {
            //$grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('carNo','车牌号码');
            $grid->column('time','入场时间')->display(function ($e){
                $seconds = bcdiv($this->time, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->column('modifyTime','出场时间')->display(function ($e){
                $seconds = bcdiv($this->modifyTime, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->column('receivableFee','应收金额')->display(function ($e){
                return !empty($this->receivableFee) ? $this->receivableFee:'免费';
            });
            $grid->disableRowSelector();
            $grid->disableColumnSelector();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('carNo','车牌号码');

            });
        });
        $card1 = Card::make('', $grid);
        return $card1;
    }

    public function tab5(){
        $grid = Grid::make(new ParkingChargeInfoTable(), function (Grid $grid) {
            //$grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('carNum','车牌号码');
            $grid->column('enterTime','入场时间')->display(function ($e){
                $seconds = bcdiv($this->enterTime, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->column('leaveTime','出场时间')->display(function ($e){
                $seconds = bcdiv($this->leaveTime, 1000,0);
                $date = date('Y-m-d H:i:s', $seconds);
                return $date;
            });
            $grid->column('receivableFee','应收金额')->display(function ($e){
                return !empty($this->receivableFee) ? $this->receivableFee:'免费';
            });
            $grid->disableRowSelector();
            $grid->disableColumnSelector();
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('carNum','车牌号码');

            });
        });
        $card1 = Card::make('', $grid);
        return $card1;
    }



}
