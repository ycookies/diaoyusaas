<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomSkuGift;
use App\Models\Hotel\RoomSkuTag;
use App\Models\Hotel\RoomSkuWhere;
use App\Merchant\Renderable\RoomSkuGiftTable;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;

class RoomSkuPriceSetController extends AdminController {
    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('房价设置')
            ->description('全部')
            ->breadcrumb(['text' => '房价设置', 'uri' => ''])
            ->body($this->pageMain());
        /*->body(function (Row $row) {

            $row->column(2, $this->subMenu());

            $row->column(10,$this->grid());
        });*/
    }

    public function pageMain() {
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->addLink('房价日历', admin_url('room-sku-calendar'));
        $tab->add('房价设置', $this->grid(), true);
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
        $user = Admin::guard()->user();
        Admin::translation('room-sku-price');
        $views = Grid::make(RoomSkuPrice::with('room'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'state'=> 1])->orderBy('room_id', 'ASC');
            $grid->column('id');
            $grid->column('room.name','房型')->setAttributes(['mergeRows'=> '1']);
            $grid->column('roomsku_title', '房型销售SKU')->display(function () {
                return '<span class="text-muted">' . $this->room->name . '</span><br/> <' . $this->roomsku_title . '><br/><span class="text-muted">' . $this->sku_code . '</span>';
            });
            $grid->column('roomsku_price', '线上日常价')->editable();
            $grid->column('roomsku_stock', '房量')->help('每天可预定房量')->editable();
            $grid->disableActions();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();

                //$actions->dis
            });
            // 工具导航栏
            /*$grid->tools(function ($tools) {
                $tools->append(new PriceSubMenu());
            });*/
            /*$grid->column('total_num');
            $grid->column('price');
            $grid->column('floor');
            $grid->column('people');
            $grid->column('state');*/
            //$grid->fixColumns(2);
            //$grid->scrollbarX();
            //$grid->disableActions();
            $grid->disableRowSelector();
            $grid->disableBatchActions();
            $grid->disableCreateButton();
            // $grid->disableRefreshButton();
            //$grid->disableRefreshButton();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('room_id', '房型')->select(Room::where(['hotel_id' => Admin::user()->hotel_id])->pluck('name', 'id'))->width(4);
                $filter->like('roomsku_title','房型销售标题')->width(4);
                $filter->between('roomsku_price','线上销售价')->width(4);
            });
        });
        $views->footer(view('merchant.subview.room-price-modal')->render());
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
    protected function form()
    {
        return Form::make(RoomSkuPrice::with('room'), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('sku_code')->value(Admin::user()->hotel_id.rand(1000,9999));
            if(!empty(request('room_id'))){
                $form->hidden('room_id')->value(request('room_id'));
                $room_name = Room::where(['id'=> request('room_id')])->value('name');
                $form->html("<h3>$room_name</h3>")->label('房型名称');
            }else{
                $form->select('room_id','选择房型')->options(Room::where(['hotel_id' => Admin::user()->hotel_id])->pluck('name','id'))->required();
            }

            $form->text('roomsku_title','销售标题')->required();
            $form->text('roomsku_price','日常销售价')->prepend('￥')->width(3)->required();
            $form->text('roomsku_stock','房量')->append('间')->required();
            $form->radio('roomsku_zaocan','早餐')->options([0=> '无早餐',1=> '1份早餐',2=> '2份早餐',3=> '3份早餐'])->required();
            // 新增时
            $form->sku('roomsku_where', '订房享受条件')->addColumn([]);
            $form->multipleSelectTable('roomsku_gift')
                ->title('选择礼包')
                ->max(4)
                ->from(RoomSkuGiftTable::make())
                ->model(RoomSkuGift::class, 'id', 'sku_gift_name');
            $form->checkbox('roomsku_tags','权益标签')->options(RoomSkuTag::all()->pluck('sku_tags_name','id'))->canCheckAll();
            $form->text('roomsku_give_points')->width(5)->help('选择一种优惠券,订房入住结束后,立即发放');
            $form->select('roomsku_give_coupon')->help('订房入住结束后，赠送积分,可用积分换实物');
            $form->switch('recommend','推荐')->help('推荐后,排名会靠前');
            $form->switch('state','状态')->default(1)->help('是否展示');
            $form->switch('is_full_room','满房状态')->help('关闭后,会展示满房图标,不可预订');

            /*$grid->column('recommend','推荐')->switch();
            $grid->column('state','状态')->switch();
            $grid->column('is_full_room','满房状态');*/
            $form->display('created_at');
            $form->saved(function (Form $form, $result) {
                // 判断是否是新增操作
                return $form->response()->success('操作成功')>redirect('/merchant/room-sku-price?room_id='.$form->room_id);
            });

        });
    }
}
