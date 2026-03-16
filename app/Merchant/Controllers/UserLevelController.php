<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers;

use App\Models\Hotel\UserLevel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Hotel\HotelSetting;

// 列表
class UserLevelController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('订房会员 管理')
            ->description('列表')
            ->breadcrumb(['text' => '订房会员 管理', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $req  = Request()->all();
        $type = request('_t', 1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->addLink('基础会员', url('/merchant/user-member'));
        $tab->addLink('订房会员', url('/merchant/user-booking-member'));
        $tab->add('会员等级权益设置',Card::make('',$this->grid()),true);
        //$tab->add('会员等级规则说明',$this->tab1());
        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    // 订房相关
    public function tab1(){
        $flds     = ['user_level_rule_decs'];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('user_level_configs');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->editor('user_level_rule_decs','会员等级规则说明');
        $form->disableResetButton();
        $card =  Card::make('',$form);
        return $card;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid =  Grid::make(new UserLevel(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'is_show'=>1])->orderBy('id', 'ASC');
            $grid->column('level_num','等级标识');
            $grid->column('level_logo','等级图标')->image('', '66');
            $grid->column('level_name','等级名称');
            //$grid->column('level_desc','等级描述');
            $grid->column('min_booking_num','最小订房次数')->help('等级自动升级所需');
            $grid->column('max_booking_num','最大订房次数')->help('等级自动升级所需');
            $grid->column('discount','订房折扣')->display(function (){
                return bcmul($this->discount,0.1,1).'折';
            });
            $grid->column('reward_points','赠送积分')->editable();
            //$grid->column('equity_desc','权益说明');
            $grid->column('buy_price','直接购买价格')->if(function (){
                return $this->level_num == 1;
            })->display('不能购买')
                ->else()
                ->prepend('¥ ');
            $grid->column('is_active','是否启用')->switch();
            //$grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableView();
            });
            $grid->enableDialogCreate(); // 打开弹窗创建
            $grid->disableRowSelector();
            //$grid->setResource('/user-level-save');
            //   快速添加
            /*$grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $request = Request();
                //$sc_id = $request->get('sc_id');
                //$hangzu_id = $request->get('hangzu_id');
                $create->text('name');
                $create->text('code');
            });*/
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
        });
        return $grid;
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
        return Show::make($id, new UserLevel(), function (Show $show) {
            $show->field('id');
            //$show->field('hotel_id');
            $show->field('level_name');
            $show->field('level_desc');
            $show->field('level_logo')->image();
            $show->field('min_booking_num','最小订房次数');
            $show->field('max_booking_num','最大订房次数');
            $show->field('created_at');
            //$show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(UserLevel::with('rights'), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('level_name')->required();
            $form->text('level_desc')->required();
            $form->photo('level_logo','等级图标')
                ->disk('hotel_'.Admin::user()->hotel_id)
                ->accept('jpg,png,jpeg')
                ->help('图标尺寸:101*32,格式：jpg,png,jpeg')
                ->nametype('datetime')
                ->saveFullUrl(true)
                ->remove(true);
            //$form->select('level_logo')->options(UserLevel::Level_logo_arr);
            /*$form->image('level_logo')->disk('admin')->width(3)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->removable()->saveFullUrl()->autoUpload()->required();
            */
            $form->text('min_booking_num','最小订房次数')->required();
            $form->text('max_booking_num','最大订房次数')->required();
            $form->rate('discount','权益订房折扣')
                ->rules('required|numeric|between:10,100', [
                    'required' => '请填写优惠折扣',
                    'numeric' => '只能是纯数字',
                    'between'   => '只能是10-100之间的数值',
                ])
                ->help('范围:10-100.例：98，就是9.8折优惠')->required();
            $form->number('reward_points','赠送积分')
                ->help('默认：10，赚送10个积分');
            $form->currency('buy_price','直接购买价格')->default(0)->help('默认:0元 不能购买');
            $form->editor('equity_desc','权益说明');
            $form->hasMany('rights', '相关权益描述', function (Form\NestedForm $form) {
                $form->row(function (Form\Row $form) {
                    $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
                    $form->text('title', '权益标题')->required();
                    $form->text('content', '权益内容')->required();
                    $form->iconimg('pic_url','权益图标')
                        ->disk('hotel_'.Admin::user()->hotel_id)
                        ->accept('jpg,png,jpeg')
                        ->help('图标尺寸:32*32,格式：jpg,png,jpeg')
                        ->nametype('datetime')
                        ->saveFullUrl(true)
                        ->remove(true);

                });
            })->useTable();
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
