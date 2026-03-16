<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Room;
use App\Models\Hotel\Room as RoomModel;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSkuPrice;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Dropdown;

class RoomSkuCalendarController extends AdminController {
    public $week_arr = [
        '周日', '周一', '周二', '周三', '周四', '周五', '周六'
    ];

    /**
     * page index
     */
    public function index(Content $content) {
        Admin::js('/js/room-sku-price.js');
        Admin::css('/css/room-price.css');
        return $content
            ->header('客房维护')
            ->description('全部')
            ->breadcrumb(['text' => '客房维护', 'uri' => ''])
            ->body($this->pageMain());
        /*->body(function (Row $row) {

            $row->column(2, $this->subMenu());

            $row->column(10,$this->grid());
        });*/
    }

    public function pageMain() {
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('房价日历', $this->grid());
        $tab->addLink('房价设置', admin_url('room-sku-price-set'));
        $tab->addLink('批量调房价', admin_url('room-sku-batch-edit'));
        return $tab->withCard();
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
    }

    public function subMenu() {
        $data['submenu'] = [
            [
                'menu_name' => '房价日历',
                'uri'       => '#',
            ],
            [
                'menu_name' => '房价设置',
                'uri'       => '#',
            ],
            [
                'menu_name' => '批量改房价',
                'uri'       => '#',
            ]
        ];
        return view('layouts.sub-menu', $data);

        /*$card =  Card::make('导航',view('layouts.sub-menu',$data));
        $card->withHeaderBorder();
        return $card;*/

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        Admin::script(<<<JS
        (function () {
            $('#datePickerInput').datetimepicker({
            locale:'zh-CN',
            format: 'YYYY-MM', // 日期格式
            allowInputToggle:true,
        });
            $('#datePickerInput').on('dp.change', function(e){
            var selectedDate = e.date.format('YYYY-MM');
            window.location.href = '/merchant/room-sku-calendar?xz_date='+selectedDate;
        });
            $('#xuanze_yearmonth').click(function(){
            $('#datePickerInput').datetimepicker('show');
        });
        })();

JS
        );
        Admin::translation('room');
        $views = Grid::make(RoomSkuPrice::with('room'), function (Grid $grid) {
            //$grid->column('id')->sortable();
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'state'=> 1])->orderBy('room_id', 'ASC');
            //$grid->column('id')->sortable();
            //$grid->column('sku_code','房间ID');
            //$grid->column('room.name', '房型');
            $grid->column('room.name','房型')->setAttributes(['mergeRows'=> '1']);
            $grid->column('roomsku_title', '房型销售SKU')->display(function () {
                return '<span class="text-muted">' . $this->room->name . '</span><br/> <' . $this->roomsku_title . '><br/><span class="text-muted">' . $this->sku_code . '</span>';
            });
            /*$grid->column('roomsku_title', '房型')->display(function (){
                return '<span class="text-muted">'.$this->room->name.'</span><br/> <'.$this->roomsku_title.'>';
            });*/
            //$calendar_arr = RoomModel::calendars(date('Y'),date('m'));
            // 选择日期
            $req     = Request();
            $xz_date = date('Y-m');
            if ($req->has('xz_date')) {
                $xz_date = $req->get('xz_date');
            }

            $calendar_arr = RoomModel::makeDate($xz_date); // 创建指定日期的数据
            foreach ($calendar_arr as $key => $item) {
                if ($item['date'] < date('Y-m-d')) {
                    //continue;
                }
                $field_name = 'days_' . str_replace('-', '_', $item['date']);
                $arrs       = explode('-', $item['date']);
                $label      = $item['week'];
                $days_str   = $arrs[1] . '-' . $arrs[2];
                $grid->column($field_name, $label . ' <br/> ' . $days_str)->display(function ($r) use ($field_name) {
                    $days_date = str_replace('days_', '', $field_name);
                    $days_date = str_replace('_', '-', $days_date);
                    $pricebiao = Roomprice::getRoomSkuDaysPrice($this->id, $days_date);
                    if (empty($pricebiao['mprice'])) {
                        $pricebiao = [
                            'mprice'         => formatFloats($this->roomsku_price),
                            'open_status'    => 1,
                            'booking_status' => 0,
                        ];
                    }
                    $online_price = !empty($pricebiao['mprice']) ? $pricebiao['mprice'] : '-';
                    $price_arr    = [
                        'online_price'   => formatFloats($online_price),
                        'Offline_price'  => '',
                        'xieyi_price'    => '',
                        'vip_price'      => '',
                        'booking_status' => !empty($pricebiao['booking_status']) ? $pricebiao['booking_status'] : '0',
                        'open_status'    => !empty($pricebiao['open_status']) ? $pricebiao['open_status'] : '0',
                    ];
                    $room_num     = RoomSkuPrice::getRoomSkuNum($this, $days_date); // 剩余房间
                    $zaocan_num   = 1; // 早餐数量

                    return RoomModel::showSkuDaysPrice($field_name, $this->room_id,$this->id, $price_arr, $room_num, $zaocan_num);
                });
            }

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $rowArray = $actions->row->toArray();
                //RoomModel::makeRoomDaysPrice($rowArray['hotel_id'],$rowArray['id']);

            });
            //$grid->fixColumns(2, 0);
            $grid->scrollbarX();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableBatchActions();
            $grid->disableCreateButton();
            //$grid->disableRefreshButton();

            $grid->disableFilterButton();
            $roomlist = RoomModel::where(['hotel_id' => Admin::user()->hotel_id])->pluck('name','id');

            $options = [''=> '全部房型'];
            if(!$roomlist->isEmpty()){
                $roomlist_arr = $roomlist->toArray();
                $new_options[''] = '全部房型';
                foreach ($roomlist_arr as $key => $names) {
                    $new_options[$key] = $names;
                }
                $options = $new_options;

            }
            $dropdown = Dropdown::make($options)
            ->button('快速选择房型')
                ->buttonClass('btn btn-primary')
                ->map(function ($label, $key) {
                    // 格式化菜单选项
                    $xz_date = request('xz_date');//保留原来查询参数
                    $urlstr = '';
                    if(!empty($xz_date)){
                        $urlstr = '&xz_date='.$xz_date;
                    }
                    $url = admin_url('room-sku-calendar?room_id='.$key.$urlstr);
                    return "<a href='$url'>{$label}</a>";
                })->click();
            $grid->tools($dropdown);
            $grid->tools('<div style="position:relative;display: inline-block"><input type="text" id="datePickerInput" style="display: none;" value="' . $xz_date . '"><a class="btn btn-white btn-outline " id="xuanze_yearmonth" href="#"><i class="fa fa-calendar-check-o"></i>选择日期 ' . $xz_date . '</a></div>');
            //$grid->disableFilter();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('room_id');
                $filter->like('roomsku_title');
                $filter->date('created_at');
            });
            //$grid->view('merchant.subview.room-price-modal');
        });
        $views->footer(view('merchant.subview.room-sku-price-modal')->render());
        return $views;
    }

    /*public function showDaysPrice($days,$room_id){
        $htmls = $days.'-'.$room_id.'-';
        $htmls .=  "线上价:388.00<br>";
        $htmls .=  "门市价:400.00<br>";
        $htmls .=  "协议价:344.00<br>";
        $htmls .=  "会员价:388.00<br>";
        return $htmls;
    }*/

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id) {
        return Show::make($id, new Room(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('name');
            $show->field('name_as');
            $show->field('price');
            $show->field('img');
            $show->field('floor');
            $show->field('people');
            $show->field('bed');
            $show->field('breakfast');
            $show->field('facilities');
            $show->field('windows');
            $show->field('logo');
            $show->field('total_num');
            $show->field('uniacid');
            $show->field('size');
            $show->field('is_refund');
            $show->field('yj_state');
            $show->field('yj_cost');
            $show->field('sort');
            $show->field('state');
            $show->field('classify');
            $show->field('rz_time');
            $show->field('update_time');
            $show->field('bed_num');
            $show->field('add_room');
            $show->field('pay_to_shop');
            $show->field('recommend');
            $show->field('area');
            $show->field('retail_price');
            $show->field('agreement_price');
            $show->field('member_price');
            $show->field('agreement_price_status');
            $show->field('member_price_status');
            $show->field('notes');
            $show->field('agreement_price_guide_status');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $seller = Admin::user();
        return Form::make(new Room(), function (Form $form) {
            $seller = Admin::user();
            $form->display('id');
            $form->hidden('hotel_id')->value($seller->hotel_id)->default($seller->id);
            $form->text('name');
            $form->text('name_as');
            $form->text('price');
            $form->text('img');
            $form->text('floor');
            $form->text('people');
            $form->text('bed');
            $form->text('breakfast');
            $form->text('facilities');
            $form->text('windows');
            $form->text('logo');
            $form->text('total_num');
            $form->text('uniacid');
            $form->text('size');
            $form->text('is_refund');
            $form->text('yj_state');
            $form->text('yj_cost');
            $form->text('sort');
            $form->text('state');
            $form->text('classify');
            $form->text('rz_time');
            $form->text('update_time');
            $form->text('bed_num');
            $form->text('add_room');
            $form->text('pay_to_shop');
            $form->text('recommend');
            $form->text('area');
            $form->text('retail_price');
            $form->text('agreement_price');
            $form->text('member_price');
            $form->text('agreement_price_status');
            $form->text('member_price_status');
            $form->text('agreement_price_guide_status');
            $form->markdown('notes');

        });
    }
}
