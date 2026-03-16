<?php

namespace App\Merchant\Controllers;

//use App\Merchant\Repositories\Room;
use App\Merchant\Extensions\Field\Unit;
use App\Models\Hotel\Room;
use App\Models\Hotel\Room as RoomModel;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use App\Models\Hotel\RoomSheshiConfig;
use App\Models\Hotel\RoomSheshi;
class RoomController extends AdminController {

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('房型管理')
            ->description('全部')
            ->breadcrumb(['text' => '房型管理', 'uri' => ''])
            ->body($this->grid());
    }

    public function curdroom() {

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $seller = Admin::user();

        $num = RoomModel::where(['hotel_id' => $seller->hotel_id])->count();
        if (empty($num)) {
            $data = RoomModel::where(['hotel_id' => 0])->get();
            foreach ($data as $key => $items) {
                $items->hotel_id = $seller->hotel_id;
                $insdata         = collect($items)->toArray();
                unset($insdata['id']);
                unset($insdata['created_at']);
                unset($insdata['created_at']);
                RoomModel::create($insdata);
            }
        }


        return Grid::make(new Room(), function (Grid $grid) {
            $seller = Admin::user();
            $grid->model()->where(['hotel_id' => $seller->hotel_id])->orderBy('id', 'ASC');
            //$grid->column('id');
            $grid->column('name_as','房间ID')->editable();
            $grid->column('name')->editable();
            $grid->column('total_num');
            //$grid->column('price')->editable();
            /*$grid->column('price_list','房价列表')
                ->display('查看')
                ->expand(function () {
                    return \App\Merchant\Renderable\PriceListTable::make()->payload(['room_id' =>$this->id]);
                });*/
            $grid->column('floor');
            $grid->column('people');
            $grid->column('recommend', '是否推荐')->switch();
            $grid->column('state')->switch();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                $actions->disableView();
                $actions->append("<a href='/merchant/room-sku-price?room_id=".$actions->row->id."' target='_blank'>&nbsp; 房价管理</a>");
                //$actions->dis
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
                $filter->equal('price');
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
    protected function detail($id) {
        return Show::make($id, new Room(), function (Show $show) {
            $show->field('id');
            //$show->field('hotel_id');
            $show->field('name');
            $show->field('logo')->image();
            $show->field('moreimg')->image();
            /*$show->field('img')->as(function ($img) {
                // 获取当前行的其他字段
                if(empty($img)){
                    return '';
                }
                return implode(',',$img);
            });*/
            $show->field('price');
            $show->field('floor');
            $show->field('people');
            $show->field('bed', ' 床数量');
            $show->field('breakfast');
            $show->field('facilities');
            $show->field('windows');

            $show->field('total_num');
            $show->field('uniacid');
            $show->field('size');
            $show->field('is_refund');
            $show->field('yj_state');
            $show->field('yj_cost');
            $show->field('sort');
            $show->field('state');
            $show->field('classify');
            //$show->field('rz_time');
            //$show->field('update_time');
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
            $show->field('notes')->unescape();
            $show->field('agreement_price_guide_status');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        Form::extend('unit', Unit::class);
        return Form::make(new Room(), function (Form $form) {

            $form->block(8, function (Form\BlockForm $form) {
                // 设置标题
                $form->title('基本设置');
                // 显示底部提交按钮
                $form->showFooter();
                // 设置字段宽度
                //$form->width(9, 2);
                //$form->display('id');
                $form->width(10,2);
                $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
                $form->text('name')->required();
                $form->text('name_as','房间编号')->help('例:A001')->required();
                $form->currency('price')->symbol('￥')->help('线上价格')->required();
                $form->switch('state')->help('正常或关闭');
                $form->switch('recommend','热门推荐');
                $form->image('logo')->disk('oss')->width(3)->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required();

                $form->multipleImage('moreimg')
                    ->disk('oss')
                    ->rules(function (Form $form) {
                        return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                    })->accept('jpg,png,gif,jpeg,webp', 'image/*')
                    ->saveFullUrl()
                    ->autoUpload()
                    ->required();
                $form->ueditor('member_rights', '会员权益')->default('# 会员权益');
                $form->ueditor('notes', '政策与服务')->default('# 政策与服务');
            });
            $form->block(4, function (Form\BlockForm $form) {

                $form->width(9, 3);
                $form->title('基本信息/各项价格');
                $form->text('floor')->append('层')->prepend('')->required();
                $form->text('area')->append('平方')->prepend('')->required();
                $form->text('people')->append('人')->prepend('')->required();
                $form->text('bed_num')->append('张')->prepend('')->required();;
                $form->text('bed_size')->append('米')->prepend('')->placeholder('例：1.2*2.0')->required();;
                $form->text('total_num', '房量')->append('套')->prepend('')->help('此类房型房量')->required();;

                $form->radio('windows')->options(['有' => '有','部分有'=>'部分有','没有' => '没有'])->required();;
                $form->radio('network','网络')->options(['WIFI免费' => 'WIFI免费','没有' => '无网络'])->required();;
                $form->radio('xiyan','吸烟政策')->options(['禁止' => '禁止','允许' => '允许',])->required();
                /*$form->radio('breakfast','免费早餐')
                    ->when('1',function (Form\BlockForm $form){
                        $form->radio('zaocan_num','早餐数量')->options(['1' => '单人','2' => '双人','3' => '3人']);
                    })
                    ->options(['1' => '有', '0' => '没有'])->required();*/
                $form->currency('yj_cost', ' 押金金额')->symbol('￥')->help('默认0,无押金')->placeholder('默认无押金')->default(0);

               /* $form->checkbox('agreement_price_status', '渠道价格')
                    ->when(1, function (Form\BlockForm $form) {
                        $form->currency('agreement_price', '协议价')->symbol('￥');
                    })
                    ->when(2, function (Form\BlockForm $form) {
                        $form->currency('member_price', '会员价')->symbol('￥');
                    })
                    ->options([
                        1 => '是否支持协议价',
                        2 => '是否支持会员价',
                    ]);*/
                $form->next(function (Form\BlockForm $form) {
                    $form->title('客房设施');
                    $form->width(9, 3);
                    $form->checkbox('wifi_network', '网络通讯')->options(RoomSheshiConfig::getSheshiGroup('wifi_network'));
                    $form->checkbox('kefang_buju', '客房布局')->options(RoomSheshiConfig::getSheshiGroup('kefang_buju'));
                    $form->checkbox('xiyu_yongpin', '洗浴用品')->options(RoomSheshiConfig::getSheshiGroup('xiyu_yongpin'));
                    $form->checkbox('kefang_sheshi', '客房设施')->options(RoomSheshiConfig::getSheshiGroup('kefang_sheshi'));
                    $form->checkbox('shipin_yinpin', '食品饮品')->options(RoomSheshiConfig::getSheshiGroup('shipin_yinpin'));
                    $form->checkbox('meiti_keji', '媒体科技')->options(RoomSheshiConfig::getSheshiGroup('meiti_keji'));
                    $form->checkbox('qingjie_fuwu', '清洁服务')->options(RoomSheshiConfig::getSheshiGroup('qingjie_fuwu'));
                    $form->checkbox('bianli_sheshi', '便利设施')->options(RoomSheshiConfig::getSheshiGroup('bianli_sheshi'));
                });
            });
            $form->saving(function (Form $form) {
                $form->img = 1;
                if ($form->isCreating()) {
                    $count = Room::where(['hotel_id'=> Admin::user()->hotel_id,'name'=>$form->name])->count();
                    if(!empty($count)){
                        return $form->response()->error('已经有相同的房型名称存在');
                    }
                }
            });
            $form->saved(function (Form $form) {
                $sheshi_group = [
                    'wifi_network',
                    'kefang_buju',
                    'xiyu_yongpin',
                    'kefang_sheshi',
                    'shipin_yinpin',
                    'meiti_keji',
                    'qingjie_fuwu',
                    'bianli_sheshi',
                ];
                foreach ($sheshi_group as $key => $group_name) {
                    if(!empty($modeldata[$group_name])){
                        $modeldata = $form->model()->toArray();
                        $group_value = json_encode($modeldata[$group_name],JSON_UNESCAPED_UNICODE);
                        if(!empty($group_value)){
                            RoomSheshi::setSheshiGroup($form->hotel_id,$group_name,$group_value);
                        }
                    }
                }
                return $form->response()->success('保存成功')->redirect('/room');
            });

        });
    }
}
