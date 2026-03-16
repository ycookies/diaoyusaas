<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Room;
use App\Models\Hotel\Room as  RoomModel;
use App\Models\Hotel\Roomprice;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Admin;
use App\Merchant\Extensions\Tools\PriceSubMenu;
use Dcat\Admin\Widgets\Tab;
use http\Env\Request;

class RoomPriceSetController extends AdminController
{
    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('房价设置')
            ->description('全部')
            ->breadcrumb(['text'=>'房价设置','uri'=>''])
            ->body($this->pageMain());
            /*->body(function (Row $row) {

                $row->column(2, $this->subMenu());

                $row->column(10,$this->grid());
            });*/
    }
    public function pageMain(){
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->addLink('房价日历',admin_url('room-calendar'));
        $tab->add('房价设置', $this->grid(),true);
        $tab->addLink('批量调房价', admin_url('room-batch-edit'));
        return $tab->withCard();
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
    }
    public function subMenu(){
        $data['submenu'] = [
            [
                'menu_name' => '房价日历',
                'uri' => '#',
            ],
            [
                'menu_name' => '房价设置',
                'uri' => '#',
            ],
            [
                'menu_name' => '批量改房价',
                'uri' => '#',
            ]
        ];
        return view('layouts.sub-menu',$data);

        /*$card =  Card::make('导航',view('layouts.sub-menu',$data));
        $card->withHeaderBorder();
        return $card;*/

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //$user = Admin::guard()->user();
        Admin::translation('room');
        //Admin::html(view('merchant.subview.room-price-modal'));
        $views = Grid::make(new Room(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','ASC');
            $grid->column('name_as','房型ID');
            $grid->column('name','房型');
            $grid->column('price','线上日常价')->editable();
            $grid->column('total_num','房间数')->help('每天可预定数')->editable();
            /*$grid->column('retail_price','门市价')->editable();
            $grid->column('agreement_price','协议价')->editable();
            $grid->column('vip_price','会员价')->editable();*/
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
                $filter->equal('id');
                $filter->like('name');
                $filter->equal('price');
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
    protected function detail($id)
    {
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
        return Form::make(new Room(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('name');
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
            $form->saving(function (Form $form) {
                $newId = $form->getKey();
                $req = Request();
                if($req->has('price')){
                    Roomprice::setRoomDefaultPrice($newId,$form->model()->price,$req->get('price'));
                }
            });
            $form->saved(function (Form $form, $result) {
                // 判断是否是新增操作

            });

        });
    }
}
