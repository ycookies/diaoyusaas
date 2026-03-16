<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\Seller as SellerModels;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;

/**
 * 门店信息
 */
class StoreInfoController extends AdminController {
    /**
     * 指定翻译文件名称
     *
     * @var string
     */
    protected $translation = 'Seller';
    private $shopinfo;


    protected $options = [
        1 => '显示文本框',
        2 => '显示编辑器',
        3 => '显示文件上传',
        4 => '还是显示文本框',
    ];

    public function index(Content $content) {
        return $content
            ->header('酒店基本信息')
            ->description('')
            ->breadcrumb(['text' => '酒店基本信息', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面主体
    public function pageMain() {
        $data = [];
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        //$tab->add('总体概况',$this->tab1());
        $tab->add('基本信息', $this->tab2(),'','info_tab2');
        $tab->add('图文简介', $this->tab8(),'','info_tab8');
        $tab->add('酒店图片', $this->tab3(),'','info_tab3');
        //$tab->add('酒店政策', $this->tab4(),'','info_tab4');
        $tab->add('酒店设施', $this->tab5(),'','info_tab5');
        $tab->add('订房必读', $this->tab6(),'','info_tab6');
        $tab->add('品牌介绍', $this->tab7(),'','info_tab7');
        $tab->add('酒店周边', $this->tab9(),'','info_tab9');
        $tab->add('酒店宣传片', $this->tab10(),'','info_tab10');
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    public function tab1() {
        $data = [];
        return view('merchant.storeinfo.tab1', $data);
    }
    // 更新酒店信息
    public function uphotelinfo(){
        $seller = Admin::user();
        $models = SellerModels::where(['name' => $seller->name])->first();
        if(!empty($models->id)){
            $models->hotel_user_id = $seller->id;
            $models->save();
        }
        return true;
    }

    public function tab2() {
        Admin::translation('seller');
        $this->uphotelinfo();
        $seller = Admin::user();
        $infos   = SellerModels::where(['hotel_user_id' => $seller->id])->first();
        if ($infos) {
            $this->shopinfo = $infos;
            $form           = Form::make(new SellerModels())->edit($infos->id);
            $form->action(url('/merchant/storeinfo/'.$infos->id));

        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        //$form->action(url('/merchant/storeinfo'));
        //$form->display('id');
        //$form->display('hotel_user_id','管理用户ID');
        $form->text('name', '酒店名称')->required();
        $form->select('store_type')->width(2)->options(SellerModels::$store_type_arr)->required();
        $form->select('star')->width(2)->options(SellerModels::$hotel_star_arr)->required();
        $form->text('store_brand');
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->date('open_time')->required();
        $form->date('decorate_time')->required();
        $form->number('room_num')->required();
        $form->text('tel')->required();
        $form->text('link_tel')->required();
        $form->text('link_name')->required();
        $form->text('coordinates','经纬度坐标')->help('<a target="_blank" href="https://lbs.qq.com/getPoint/"> >>> 去拾取位置坐际</a>')->required();
        $form->text('address')->required();
        $form->text('store_wifi_password','门店大堂Wifi密码');
        $form->textarea('tese_info','酒店特色');
        $form->textarea('overview','酒店概览');
        $form->hidden('act_type')->value('base')->required();
        $form->confirm('您确定要现在保存吗？', 'content');
        /*Form::make(new Seller(), function (Form $form) {

        })*/
        /*$form->text('name','酒店名称')->required();
        $form->text('name','酒店名称')->required();
        $form->text('name','酒店名称')->required();*/

        $Card = Card::make('基本项', $form);

        return $Card;
    }

    // 酒店图片
    public function tab3() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }

        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->image('ewm_logo')->width(3)->help('建议尺寸大小:541*500')->saveFullUrl()->url('/upload/imgs')->autoUpload()->removable(false)->autoSave(false)->required();
        $form->multipleImage('img', '酒店场景图')->help('建议尺寸大小:220*180')->saveFullUrl()->url('/upload/imgs')->autoUpload()->removable(false)->autoSave(false)->required();
        $form->confirm('您确定要现在保存吗？', 'content');
        $form->hidden('act_type')->value('shopimg')->required();
        $form->saved(function (Form $form) {
            return $form->response()->success('保存成功');
        });
        $Card = Card::make('图片项', $form);

        return $Card;
    }

    // 政策
    public function tab4() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        //$form->action(url('/merchant/storeinfo'));
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('zhengce')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        $form->column(6, function (Form $form) {
            $form->html('<h3>入离时间</h3>');
            $form->width(4)->time('early_arrival_time')->required();
            $form->width(4)->time('late_arrival_time')->required();
            $form->width(4)->time('early_departure_time')->required();
            $form->width(4)->time('late_departure_time')->required();
            $form->html('<h3>退订&取消</h3>');
            $form->width(4)->select('non_cancelling_time')->options(SellerModels::$non_cancelling_time_arr)->required();
            $form->width(9)->text('non_cancelling_explain')->required();
            $form->textarea('rule')->rows(3)->required();
        });
        $form->column(6, function (Form $form) {
            $form->html('<h3>儿童政策</h3>');
            $form->html('不接受18岁以下客人在无监护人的陪同的情况下入住', $label = '');
            $form->html('<h3>宠物政策</h3>');
            $form->radio('pet', '')->options(SellerModels::$pet_arr);
            $form->html('<h3>信用卡支付</h3>');
            $form->radio('card_support', '')
                ->when('=', 2, function (Form $form) {
                    $form->checkbox('card_type', '')->options(SellerModels::$card_type_arr);
                })
                ->when(1, function (Form $form) {
                    //$form->editor('editor');
                })
                ->options(SellerModels::$card_support_arr)
                ->default(2);
            $form->html('<h3>第三方支付</h3>');
            $form->radio('otherpay_support', '')
                ->when(2, function (Form $form) {
                    $form->checkbox('otherpay_type', '')->options(SellerModels::$otherpay_type_arr);
                })
                ->when(1, function (Form $form) {
                })
                ->options(SellerModels::$otherpay_support_arr)
                ->default(2);
        });
        /*

        $form->row(function (Form\Row $form) {

        });
        $form->row(function (Form\Row $form) {


        });*/
        $form->confirm('您确定要现在保存吗？', 'content');

        $Card = Card::make('政策相关配置', $form);
        return $Card;
    }

    // 酒店设施
    public function tab5() {
        $seller = Admin::user();
        /* 更多设施 */
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('sheshi')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        //$form->checkbox('wake', '设施服务')->options(SellerModels::$hotel_facility_arr)->canCheckAll();
        $form->ueditor('service_facility', '更多设施');
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card1 = Card::make('', $form->render())->withHeaderBorder();

        /* 亮点设施 */
        $grid =  Grid::make(new \App\Models\Hotel\OffsiteFacility(), function (Grid $grid) {
            $grid->setResource('/facilitys');
            //$grid->number();
            //$grid->disableActions();
            $grid->disableRefreshButton();
            $grid->enableDialogCreate();
            $grid->disableColumnSelector();
            $grid->disableFilterButton();
            $grid->disableRowSelector();
            $grid->showQuickEditButton();
            $grid->disablePagination();
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id]);
            $grid->column('icon','图标')->image('','44');
            $grid->column('name','设施名');
            $grid->column('description','描述')->limit(20);
            $grid->column('sorts','排序')->help('数值大靠前')->editable();
            $grid->column('is_free','是否收费')->switch();
            $grid->column('is_recommend','推荐')->switch();
            $grid->column('is_show','是否有')->switch();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
        });
        $Card = Card::make('', $grid)->withHeaderBorder();

        $tab  = Tab::make();
        $tab->add('亮点设施',$Card);
        $tab->add('更多设施',$Card1);
        //$Card->title('酒店设施/服务设施');
        //$Card->style('bg-success', true);
        return $tab->render();
    }

    // 酒店设施
    public function tab6() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('prompt')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        $form->ueditor('prompt', '订房必读');
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card = Card::make('', $form);
        //$Card->title('酒店设施/服务设施');
        $Card->style('bg-success', true);
        return $Card;
    }

    // 酒店设施
    public function tab7() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('brand_info')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        $form->ueditor('brand_info', '品牌介绍');
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card = Card::make('', $form);
        //$Card->title('酒店设施/服务设施');
        $Card->style('bg-success', true);
        return $Card;
    }
    // 酒店设施
    public function tab8() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('introduction')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        $form->ueditor('introduction', '酒店简介');
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card = Card::make('', $form);
        //$Card->title('酒店设施/服务设施');
        $Card->style('bg-success', true);
        return $Card;
    }
    // 酒店设施
    public function tab9() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('zhoubian')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        $form->ueditor('zhoubian', '酒店周边');
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card = Card::make('', $form);
        //$Card->title('酒店设施/服务设施');
        $Card->style('bg-success', true);
        return $Card;
    }

    // 酒店宣传片
    public function tab10() {
        $seller = Admin::user();
        if (!empty($this->shopinfo->id)) {
            $form = Form::make(new SellerModels())->edit($this->shopinfo->id);
            $form->hidden('id')->value($this->shopinfo->id)->required();
            $form->action(url('/merchant/storeinfo/'.$this->shopinfo->id));
        } else {
            $form = Form::make(new SellerModels());
            $form->action(url('/merchant/storeinfo'));
        }
        $form->disableDeleteButton();
        $form->disableListButton();
        $form->disableViewButton();
        $form->disableResetButton();
        $form->disableViewButton();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->hidden('hotel_user_id')->value($seller->id);
        $form->hidden('act_type')->value('hotel_video')->required();
        //$form->hidden('id')->value($this->shopinfo->id)->required();
        //$form->file('video_url', '宣传片视频');
        $form->video('video_url','宣传片视频')
            ->disk('hotel_'.Admin::user()->hotel_id)
            ->path('video')
            ->nametype('datetime')
            ->saveFullUrl(true)
            ->remove(true)
            ->help('视频尺寸：1024 × 576,视频大小限制：100M以内');
        $form->photo('video_cover','视频封面')
            ->width(6)
            ->disk('hotel_'.Admin::user()->hotel_id)
            ->accept('jpg,png,jpeg')
            ->help('图标尺寸:1024 × 576,格式：jpg,png,jpeg')
            ->nametype('datetime')
            ->saveFullUrl(true)
            ->remove(true);

        //$form->image('video_cover', '视频封面')->saveFullUrl()->url('/upload/imgs')->autoUpload()->removable(false)->autoSave(false)->required();
        $form->confirm('您确定要现在保存吗？', 'content');
        $Card = Card::make('宣传片视频', $form);
        //$Card->title('酒店设施/服务设施');
        $Card->style('bg-success', true);
        return $Card;
    }

    /**
     * 酒店信息保存
     * author eRic
     * dateTime 2023-04-29 08:22
     */
    public function infosave(Request $request) {
        $seller = Admin::user();
        $request->validate([
            'id'       => 'required', // unique:connection.hotel.yx_seller,id
            'act_type' => 'required',
        ], [
            'id.required'       => 'id不能为空',
            'id.unique'         => '本酒店信息未查到',
            'act_type.required' => '操作类型 不能空',
        ]);
        $id       = $request->id;
        $act_type = $request->act_type;
        $models   = SellerModels::find($id);
        /**
         * $form->text('name', '酒店名称')->required();
         * $form->select('store_type')->width(2)->options(SellerModels::$store_type_arr)->required();
         * $form->select('star')->width(2)->options(SellerModels::$hotel_star_arr)->required();
         * $form->text('store_brand');
         * $form->datetime('open_time')->required();
         * $form->datetime('decorate_time')->required();
         * $form->number('room_num')->required();
         * $form->text('tel')->required();
         * $form->text('link_tel')->required();
         * $form->text('link_name')->required();
         * $form->text('coordinates')->required();
         * $form->text('address')->required();
         * $form->editor('introduction');
         */
        switch ($act_type) {
            case 'base':
                $request->validate([
                    'name'          => 'required',
                    'store_type'    => 'required',
                    'star'          => 'required',
                    'store_brand'   => 'required',
                    'open_time'     => 'required',
                    'decorate_time' => 'required',
                    'room_num'      => 'required',
                    'tel'           => 'required',
                    'link_tel'      => 'required',
                    'link_name'     => 'required',
                    'coordinates'   => 'required',
                    'address'       => 'required',
                    //'overview'  => 'required',
                ], [
                    'name.required' => '酒店logo 不能为空',
                    'img.required'      => '酒店场景图 不能空',
                ]);
                $models->ewm_logo = $request->ewm_logo;
                $models->img      = $request->img;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'shopimg':
                $request->validate([
                    'ewm_logo' => 'required',
                    'img'      => 'required',
                ], [
                    'ewm_logo.required' => '酒店logo 不能为空',
                    'img.required'      => '酒店场景图 不能空',
                ]);
                $models->ewm_logo = $request->ewm_logo;
                $models->img      = $request->img;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'zhengce':
                $request->validate([
                    'early_arrival_time'     => 'required',
                    'late_arrival_time'      => 'required',
                    'early_departure_time'   => 'required',
                    'late_departure_time'    => 'required',
                    'pet'                    => 'required',
                    'card_support'           => 'required',
                    'otherpay_support'       => 'required',
                    'card_type'              => 'required_if:card_support,2',
                    'otherpay_type'          => 'required_if:otherpay_support,2',
                    'non_cancelling_time'    => 'required',
                    'non_cancelling_explain' => 'required',
                    'rule'                   => 'required',
                ], [
                    'early_arrival_time.required'     => '最早入店 不能为空',
                    'late_arrival_time.required'      => '最晚入店 不能空',
                    'early_departure_time.required'   => '最早离店 不能空',
                    'late_departure_time.required'    => '最晚离店 不能空',
                    'pet.required'                    => '宠物政策 不能空',
                    'card_support.required'           => '请选择是否支持 信用卡支付 ',
                    'otherpay_support.required'       => '请选择是否支持 第三方支付',
                    'card_type.required_if'           => '请选择支持哪些 信用卡支付',
                    'otherpay_type.required_if'       => '请选择支持哪些 第三方支付',
                    'non_cancelling_time.required'    => '请选择 不可取消时间',
                    'non_cancelling_explain.required' => '请填写 不可取消说明',
                    'rule.required'                   => '请填写 退订规则',
                ]);
                $time_array = $request->only(['early_arrival_time', 'late_arrival_time', 'early_departure_time', 'late_departure_time']);
                $card_type  = '';
                if ($request->card_support == 2) {
                    $card_type = implode(',', $request->card_type);
                }
                $otherpay_type = '';
                if ($request->otherpay_support == 2) {
                    $otherpay_type = implode(',', $request->otherpay_type);
                }
                $models->arrival_departure_time = implode('-', $time_array);
                $models->pet                    = $request->pet;
                $models->card_support           = $request->card_support;
                $models->otherpay_support       = $request->otherpay_support;
                $models->card_type              = $card_type;
                $models->otherpay_type          = $otherpay_type;
                $models->non_cancelling_time    = $request->non_cancelling_time;
                $models->non_cancelling_explain = $request->non_cancelling_explain;
                $models->rule                   = $request->rule;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'sheshi':
                /**
                 * $form->checkbox('hotel_facility', '酒店设施')->options(SellerModels::$hotel_facility_arr);
                 * $form->checkbox('service_facility', '服务设施')->options(SellerModels::$service_facility_arr);
                 */
                $request->validate([
                    'wake'             => 'required',
                    'service_facility' => 'required',
                ], [
                    'wake.required'             => '请选择 酒店设施 不能空',
                    'service_facility.required' => '请选择 服务设施 不能空',
                ]);
                $models->wake             = implode(',', $request->wake);
                $models->service_facility = implode(',', $request->service_facility);
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'prompt': // 订房必读
                $models->prompt             = $request->prompt;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'brand_info': // brand_info
                $models->brand_info             = $request->brand_info;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'introduction': // brand_info
                $models->introduction             = $request->introduction;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            case 'zhoubian': // brand_info
                $models->zhoubian             = $request->zhoubian;
                if ($models->save()) {
                    return JsonResponse::make()->data($request->all())->success('成功！');
                }
                break;
            default:
                break;
        }
        return JsonResponse::make()->data($request->all())->error('保存失败');
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
        return Show::make($id, new SellerModels(), function (Show $show) {
            $show->field('id');
            $show->field('type');
            $show->field('hotel_user_id');
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
        return Form::make(new SellerModels(), function (Form $form) {
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
            $form->text('overview');
            $form->text('tese_info');
            $form->text('zhoubian');
            $form->text('img');
            $form->text('rule');
            $form->text('prompt');
            $form->text('brand_info');
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
            $form->text('video_url');
            $form->text('video_cover');
            $form->text('card_support');
            $form->text('card_type');
            $form->text('otherpay_support');
            $form->text('otherpay_type');
            //$form->text('arrival_departure_time');
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
            $form->text('store_wifi_password');
            $request = Request();
            if($request->has('early_arrival_time')){
                $time_array = $request->only(['early_arrival_time', 'late_arrival_time', 'early_departure_time', 'late_departure_time']);
                $mks = implode('-', $time_array);
                $form->text('arrival_departure_time')->value($mks)->default($mks);
            }
            $form->saved(function (Form $form) {
                $req = Request();
                // 更新订单完成
                //Cache::delete('webinfo');
                return $form->response()->success('保存成功');
            });
        });
    }
}
