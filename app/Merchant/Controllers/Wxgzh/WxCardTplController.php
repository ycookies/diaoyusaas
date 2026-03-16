<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers\Wxgzh;

use App\Merchant\Actions\Form\FormSetActivationForm;
use App\Merchant\Actions\Form\WxCardCodeDeposit;
use App\Merchant\Actions\Form\WxCardQrcodeCreate;
use App\Merchant\Renderable\CardCodePre;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\WxCardTpl;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Hotel\UserMember;
use Dcat\Admin\Widgets\Alert;
// 微信会员卡-卡券
class WxCardTplController extends AdminController {
    public $wxOpen;

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('微信会员卡券管理')
            ->description('全部')
            ->breadcrumb(['text' => '列表', 'uri' => ''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {


        $grid =  Grid::make(new WxCardTpl(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('brand_name');
            $grid->column('title');
            $grid->column('header_image','用户会员卡片头部图片')->image('', '100px')->width('100px');
            $grid->column('background_pic_url')->image('', '100px')->width('100px');
            $grid->column('logo_url','卡券logo')->image('', '66px')->width('66px');
            $grid->column('quantity', '卡号库存')->display(function () {
                $hotel_id = Admin::user()->hotel_id;
                $quantity = '';
                if ($this->card_id != '') {
                    $wxOpen = app('wechat.open');
                    $wxgzh    = $wxOpen->hotelWxgzh($hotel_id);
                    if($wxgzh !== false){
                        $res = $wxgzh->card->get($this->card_id);
                    }
                    if(!empty($res)){
                        if (!empty($res['card']['member_card']['base_info']['sku']['quantity'])) {

                            $quantity = $res['card']['member_card']['base_info']['sku']['quantity'];
                        } else {
                            $quantity = 'error';
                        }
                    }


                }
                return $quantity;
            });
            $grid->column('code_pre_list', '卡号预存')->display('查看')
                //->modal(CardCodePre::make());
                ->modal(function ($modal) {
                    // 设置弹窗标题
                    $modal->title('卡号预存');
                    // 自定义图标
                    $modal->icon('feather icon-chevrons-right');
                    $card = new Card(null, CardCodePre::make()->payload(['card_id' => $this->card_id]));
                    return "<div style='padding:10px 10px 0'>$card</div>";
                });
            $grid->column('card_id')->limit(10);
            $grid->column('status')->using(WxCardTpl::Status_arr);
            $grid->column('created_at');
            //$grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                $actions->disableView();

                if ($actions->row->card_id == '') {
                    // 提交小程序模板
                    $form1 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
                    $form1->action('/wxgzh/card/addCard');
                    //$form1->confirm('确认现在提交吗？');
                    $form1->html('提交到微信创建卡券');
                    $form1->hidden('card_tpl_id', '描述')->value($actions->row->id);
                    $form1->disableResetButton();
                    $modal = Modal::make()
                        ->title('提交微信创建')
                        ->body($form1)
                        ->button('<i class="feather icon-corner-right-up tips" data-title="提交微信创建"></i> 提交微信创建 &nbsp;&nbsp;');
                    $actions->append($modal);

                }
                if ($actions->row->card_id != '') {
                    // 删除创建
                    $form2 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
                    $form2->action('/wxgzh/card/delCard');
                    //$form1->confirm('确认现在提交吗？');
                    $form2->html('删除微信卡券');
                    $form2->hidden('card_id', '描述')->value($actions->row->card_id);
                    $form2->disableResetButton();
                    $modal = Modal::make()
                        ->title('删除微信卡券')
                        ->body($form2)
                        ->button('<i class="feather icon-delete tips" data-title="删除微信卡券"></i> 删除微信卡券 &nbsp;&nbsp;');
                    $actions->append($modal);

                    $modal = Modal::make()
                        ->title('获取领取会员卡 二维码')
                        ->body(WxCardQrcodeCreate::make()->payload(['hotel_id' => $actions->row->hotel_id, 'card_id' => $actions->row->card_id]))
                        ->button('<i class="feather icon-command tips" data-title="获取领取二维码"></i> 获取领取二维码 &nbsp;&nbsp;');
                    $actions->append($modal);


                    $modal2 = Modal::make()
                        ->lg()
                        ->title('导入会员卡-卡号')
                        ->body(WxCardCodeDeposit::make()->payload(['hotel_id' => $actions->row->hotel_id, 'card_id' => $actions->row->card_id]))
                        ->button('<i class="feather icon-box tips" data-title="导入会员卡-卡号"></i> 导入会员卡 &nbsp;&nbsp;');
                    $actions->append($modal2);

                    $modal2 = Modal::make()
                        ->lg()
                        ->title('设置用户开卡字段')
                        ->body(FormSetActivationForm::make()->payload(['hotel_id' => $actions->row->hotel_id, 'card_id' => $actions->row->card_id]))
                        ->button('<i class="feather icon-life-buoy tips" data-title="设置开卡字段"></i> 设置开卡字段 &nbsp;&nbsp;');
                    $actions->append($modal2);
                }
            });
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            /*$grid->wrap(function (Renderable $view) {
                $tab = Tab::make();
                $tab->addLink('超级VIP会员卡', admin_url('member-vip'));
                $tab->add('微信卡券', $view, true);
                $tab->addLink('购买订单', admin_url('member-order'));
                return $tab;
            });*/
        });

        $htmll = <<<HTML
<ol>
    <li>第一步：设置卡券商户名字,卡券名,卡券背景图，卡券logo</li>
    <li>第二步：提交到微信卡券接口去创建</li>
    <li>第三步：设置用户开卡字段</li>
    <li>第四步：导入会员卡号，预存一批卡号待用户随机领取</li>
    <li>第五步：以上<span class="text-danger">四步完成后</span>，用户才能在小程序正常开卡领取会员卡</li>
</ol>
HTML;
        $alert = Alert::make($htmll, '提示:');

        return $alert->info().$grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id) {
        return Show::make($id, new WxCardTpl(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('brand_name');
            $show->field('title');
            $show->field('background_pic_url');
            $show->field('logo_url');
            $show->field('colors');
            $show->field('notice');
            $show->field('description');
            $show->field('service_phone');
            $show->field('prerogative');
            $show->field('quantity');
            $show->field('discount');
            $show->field('qrcode_url');
            $show->field('testwhitelist');
            $show->field('form_data');
            $show->field('response_data');
            $show->field('card_id');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        return Form::make(new WxCardTpl(), function (Form $form) {
            $form->column(6, function (Form $form) {
                $hotelInfo = Hotel::where(['id' => Admin::user()->hotel_id])->select('id', 'name', 'tel')->first();
                $form->width(9, 3);
                //$form->display('id');
                $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
                $form->text('brand_name')->help('字数上限为12个汉字')
                    ->default($hotelInfo->name)
                    ->rules('max:12',[
                    'max'=> '商户名字 最多12个字',
                ])->required();
                $form->text('title')->default('悦享会员卡')->help('字数上限为9个汉字。例：悦享会员卡')
                    ->rules('max:9',['max'=> '卡券名 最多9个字'])
                    ->required();
                $form->text('service_phone')->default($hotelInfo->tel)
                    ->required();
                $form->select('colors')
                    ->options(WxCardTpl::Color_arr)
                    ->default('Color040')
                    ->help('<a href="/img/colors.jpeg" target="_blank">>>查看色值</a>')->required();
                $form->hidden('notice')->default('使用时向服务员出示此券')->help('字数上限为16个汉字');
                $form->textarea('description')->default('小程序订房享折扣优惠')->help('字数上限为1024个汉字');
                $form->textarea('prerogative')->default('会员卡特权说明')->help('字数上限制1024汉字');
                $form->textarea('balance_rules', '储值规则')->default('储值说明')->default('每次储值享最多优惠')->help('字数上限制128汉字');
                //$form->text('quantity')->default(90000000)->help('默认');
                $form->rate('discount','折扣优惠')
                    ->default(1)
                    ->help('默认为1, 9.9折优惠。如填10 就是9折优惠')
                    ->rules('between:1,90', [
                    'between' => '只能是1-90的数值',
                ])->required('between:1,90');
                $form->image('header_image','用户会员卡片头部图片')
                    ->dimensions(['width' => 600, 'height' => 200])->help('尺寸:600*200')
                    ->default('https://hotel.rongbaokeji.com/img/card-modal-bg.png')
                    ->url('upload/imgs')
                    ->uniqueName()
                    ->removable(false)
                    ->autoUpload()
                    ->saveFullUrl()->required();

                $form->image('background_pic_url')->dimensions(['width' => 1000, 'height' => 600])
                    ->disk('public')
                    //->url('upload/imgs')
                    ->uniqueName()->removable(false)->autoUpload()->saveFullUrl()
                    ->help('会员卡背景图 1000*600 自定义背景设计规范 <a href="https://mp.weixin.qq.com/cgi-bin/readtemplate?t=cardticket/card_cover_tmpl&type=info&lang=zh_CN" target="_blank">查看</a>')->default(asset('img/card-bg2.png'));
                $form->image('logo_url')
                    //->disk('public')
                    ->url('upload/storage')
                    ->uniqueName()->removable(false)->autoUpload()->saveFullUrl()
                    ->dimensions(['width' => 300, 'height' => 300])->help('卡券的商户logo，建议像素为300*300')->default(asset('img/card-logo.png'));
            });
            $form->column(6, function (Form $form) {
                $form->html('<img width="100%" src="' . asset('/img/card-demo.png') . '" />');
            });

        });
    }
}
