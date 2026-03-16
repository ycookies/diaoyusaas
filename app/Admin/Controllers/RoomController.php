<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Layout\Row;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\Room as RoomModel;
use App\Admin\Metrics\Admin\TotalRoom;
use App\Admin\Metrics\Admin\TotalBookingRoom;
use App\Admin\Metrics\Admin\TotalSaleRoom;
use App\Merchant\Extensions\Field\Unit;
use App\Models\Hotel\RoomSheshiConfig;
use App\Models\Hotel\RoomSheshi;
class RoomController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('客房管理')
            ->description('全部')
            ->breadcrumb(['text'=>'客房管理','uri'=>''])
            ->body(function (Row $row) {
                $row->column(4, new TotalRoom());
                $row->column(4, new TotalBookingRoom());
                $row->column(4, new TotalSaleRoom());
            })
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //$user = Admin::guard()->user();

        /**
         * ->modal(function (Grid\Displayers\Modal $modal){
        $modal->xl();
        // 标题
        $modal->title('弹窗标题');
        // 自定义图标
        $modal->icon('feather icon-edit');
        // 传递当前行字段值
        return UserProfile::make()->payload(['name' => $this->name]);
        })
         */
        return Grid::make(RoomModel::with('hotel'), function (Grid $grid) {
            $grid->model()->where([['hotel_id','>',100]])->orderBy('id','DESC');
            $grid->column('id');
            //$grid->column('hotel.name','酒店');
            //$grid->column('name','客房名称');
            $grid->column('logo','客房主图')->image('','100px')->width('100px');
            $grid->column('hotel_room','酒店/客房名称')->display(function (){
                $hotel_name = !empty($this->hotel->name) ? $this->hotel->name:'';
                $htmls =  '<span class="text-muted">'.$hotel_name.'</span>';
                $htmls .= '<br/><a class="text-dark" href="'.admin_url('/room/'.$this->id).'" >'.$this->name.'</a>';
                return $htmls;
            });
            $grid->column('total_num');
            $grid->column('price');
            $grid->column('floor');
            $grid->column('people');
            $grid->column('recommend','是否推荐')->bool();
            $grid->column('state')->using([0=>'关闭',1=>'开启'])->dot(
                [
                    0 => 'danger',
                    1 => 'success',
                ],
                'primary' // 第二个参数为默认值
            );
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();

                //$actions->disableView();
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('hotel_id','酒店')
                    ->select(Hotel::where([['id','>',100]])->pluck('name','id'))->width(3);
                $filter->equal('id','客房ID')->width(3);
                $filter->like('name')->width(3);
                $filter->between('price')->width(3);
            });
        });
    }


    // 查看
    public function show($id, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['show'] ?? trans('admin.show'))
            ->body($this->detail($id));
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail_back($id)
    {
        return Show::make($id, RoomModel::with('hotel'), function (Show $show) {
            $show->field('id');
            $show->field('hotel.name','酒店名');
            $show->field('name');
            $show->field('logo')->image();
            $show->field('moreimg')->as(function ($img) {
                // 获取当前行的其他字段
                if(empty($img)){
                    return '';
                }
                return implode(',',$img);
            })->image();
            $show->field('price');
            $show->field('floor');
            $show->field('people');
            $show->field('bed');
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
    protected function detail($id) {
        Form::extend('unit', Unit::class);
        return Form::make(new RoomModel(), function (Form $form) {
            $form->block(8, function (Form\BlockForm $form) {

                // 设置标题
                $form->title('基本设置');
                // 设置字段宽度
                //$form->width(9, 2);
                //$form->display('id');
                $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
                $form->text('name')->required();
                $form->text('name_as','房间别称')->required();
                $form->currency('price')->symbol('￥')->help('线上价格')->required();
                $form->switch('state')->help('正常或关闭');
                $form->switch('recommend','热门推荐');
                $form->image('logo')->disk('admin')->width(3)->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required();
                $form->multipleImage('moreimg')->disk('admin')->saveFullUrl()->autoUpload()->required();

                $form->markdown('member_rights', '会员权益');
                $form->markdown('notes', '政策与服务');
            });
            $form->block(4, function (Form\BlockForm $form) {

                $form->width(9, 3);
                $form->title('基本信息/各项价格');
                $form->text('floor')->append('层')->prepend('');
                $form->text('area')->append('平方')->prepend('');
                $form->text('people')->append('人')->prepend('');
                $form->text('bed_num')->append('张')->prepend('');
                $form->text('bed_size')->append('米')->prepend('')->placeholder('例：1.2*2.0');
                $form->text('total_num', '房型数量')->append('套')->prepend('')->help('此类房型的数量');

                $form->radio('windows')->options(['有' => '有','部分有'=>'部分有','没有' => '没有']);
                $form->radio('network','网络')->options(['WIFI免费' => 'WIFI免费','没有' => '无网络']);
                $form->radio('xiyan','吸烟政策')->options(['允许' => '允许','禁止' => '禁止']);
                $form->radio('breakfast','免费早餐')
                    ->when('1',function (Form\BlockForm $form){
                        $form->radio('zaocan_num','早餐数量')->options(['1' => '单人','2' => '双人','3' => '3人']);
                    })
                    ->options(['1' => '有', '0' => '没有']);
                $form->currency('yj_cost', ' 押金金额')->symbol('￥')->help('默认0,无押金')->placeholder('默认无押金')->default(0);

                $form->checkbox('agreement_price_status', '渠道价格')
                    ->when(1, function (Form\BlockForm $form) {
                        $form->currency('agreement_price', '协议价')->symbol('￥');
                    })
                    ->when(2, function (Form\BlockForm $form) {
                        $form->currency('member_price', '会员价')->symbol('￥');
                    })
                    ->options([
                        1 => '是否支持协议价',
                        2 => '是否支持会员价',
                    ]);
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
            $form->disableResetButton();
            $form->disableSubmitButton();
            $form->saving(function (Form $form) {
                $form->img = 1;
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
                    $modeldata = $form->model()->toArray();
                    $group_value = json_encode($modeldata[$group_name],JSON_UNESCAPED_UNICODE);
                    RoomSheshi::setSheshiGroup($form->hotel_id,$group_name,$group_value);
                }

                return $form->response()->success('保存成功')->refresh();
            });

        })->edit($id);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        Form::extend('unit', Unit::class);
        return Form::make(new RoomModel(), function (Form $form) {
            $form->block(8, function (Form\BlockForm $form) {
                // 设置标题
                $form->title('基本设置');
                // 显示底部提交按钮
                $form->showFooter();
                // 设置字段宽度
                //$form->width(9, 2);
                //$form->display('id');
                $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
                $form->text('name')->required();
                $form->text('name_as','房间别称')->required();
                $form->currency('price')->symbol('￥')->help('线上价格')->required();
                $form->switch('state')->help('正常或关闭');
                $form->switch('recommend','热门推荐');
                $form->image('logo')->disk('admin')->width(3)->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required();
                $form->multipleImage('moreimg')->disk('admin')->saveFullUrl()->autoUpload()->required();

                $form->markdown('member_rights', '会员权益');
                $form->markdown('notes', '政策与服务');
            });
            $form->block(4, function (Form\BlockForm $form) {

                $form->width(9, 3);
                $form->title('基本信息/各项价格');
                $form->text('floor')->append('层')->prepend('');
                $form->text('area')->append('平方')->prepend('');
                $form->text('people')->append('人')->prepend('');
                $form->text('bed_num')->append('张')->prepend('');
                $form->text('bed_size')->append('米')->prepend('')->placeholder('例：1.2*2.0');
                $form->text('total_num', '房型数量')->append('套')->prepend('')->help('此类房型的数量');

                $form->radio('windows')->options(['有' => '有','部分有'=>'部分有','没有' => '没有']);
                $form->radio('network','网络')->options(['WIFI免费' => 'WIFI免费','没有' => '无网络']);
                $form->radio('xiyan','吸烟政策')->options(['允许' => '允许','禁止' => '禁止']);
                $form->radio('breakfast','免费早餐')
                    ->when('1',function (Form\BlockForm $form){
                        $form->radio('zaocan_num','早餐数量')->options(['1' => '单人','2' => '双人','3' => '3人']);
                    })
                    ->options(['1' => '有', '0' => '没有']);
                $form->currency('yj_cost', ' 押金金额')->symbol('￥')->help('默认0,无押金')->placeholder('默认无押金')->default(0);

                $form->checkbox('agreement_price_status', '渠道价格')
                    ->when(1, function (Form\BlockForm $form) {
                        $form->currency('agreement_price', '协议价')->symbol('￥');
                    })
                    ->when(2, function (Form\BlockForm $form) {
                        $form->currency('member_price', '会员价')->symbol('￥');
                    })
                    ->options([
                        1 => '是否支持协议价',
                        2 => '是否支持会员价',
                    ]);
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
                    $modeldata = $form->model()->toArray();
                    $group_value = json_encode($modeldata[$group_name],JSON_UNESCAPED_UNICODE);
                    RoomSheshi::setSheshiGroup($form->hotel_id,$group_name,$group_value);
                }

                return $form->response()->success('保存成功')->refresh();
            });

        });
    }
}
