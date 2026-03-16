<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\User;
use App\Models\Hotel\UserMember;
use App\Models\Hotel\UserLevel;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Modal;

class UserMemberController extends AdminController {
    protected $translation = 'user';
    protected $title = '用户/会员';

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('用户/会员 管理')
            ->description('列表')
            ->breadcrumb(['text' => '用户/会员 管理', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $req  = Request()->all();
        $type = request('_t', 1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab2_active = false;
        if(!empty(request('tab')) && request('tab') == 2){
            $tab2_active = true;
        }
        $tab->add('基础会员', $this->tab1());
        $tab->addLink('订房会员', url('/merchant/user-booking-member'));
        $tab->addLink('会员等级权益设置', url('/merchant/user-level'));
        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function tab1() {
        $grid =  Grid::make(User::with('level'), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id','系统ID');
            $grid->column('avatar', '头像')->image('', '44');
            $grid->column('nick_name','用户名')->modal(function ($modal) {
                // 设置弹窗标题
                $modal->title('用户信息');
                // 自定义图标
                $modal->icon('');
                $level_name = !empty($this->level->level_name) ? $this->level->level_name:'普通用户';
                $htmls = <<<HTML
                <ul>
                <li>用户ID: $this->id</li>
                <li>用户名: $this->nick_name</li>
                <li>用户等级: {$level_name}</li>
                <li>会员卡号: $this->card_code</li>
                <li>联系电话: $this->phone</li>
                <li>联系邮箱: $this->email</li>
                <li>上次登录: $this->last_login_at ip: $this->last_login_ip</li>
                <li>账户余额: $this->balance</li>
                <li>账户积分: $this->point</li>
                <li>上级: {$this->parent_id}</li>
               
</ul>
HTML;

                $card = new Card(null, $htmls);
                return "<div style='padding:10px 10px 0'>$card</div>";
            });
            $grid->column('name', '用户信息')->display(function (){
                $level_name = !empty($this->level->level_name) ? $this->level->level_name:'普通用户';
                //$htmls = '<span class="text-gray"> 昵称:</span> '.$this->nick_name.' <br/>';
                $htmls = '<span class="text-gray">真实姓名:</span> '.$this->zs_name.' <br/>';
                $htmls .= '<span class="text-gray">手机号:</span> '.$this->phone.'<br/>';
                $htmls .= '<span class="text-gray">用户等级:</span> '.$level_name.'<br/>';
                $htmls .= '<span class="text-gray">ID编号:</span> '.$this->id.'<br/>';
                $htmls .= '<span class="text-gray">上级:</span> '.$this->parent_id.'<br/>';
                return $htmls;
            });
            /*$grid->column('zs_name', '真实姓名');
            $grid->column('phone', '手机号');*/
            //$grid->column('user_source', '用户来源')->using(User::User_source_arr);
            //$grid->column('level.level_logo', '用户等级')->image('','32');
            //$grid->column('card_code', '会员卡号');
            $grid->column('booking_num', '订房次数');
            $grid->column('point', '积分');
            $grid->column('balance', '余额');
            $grid->column('status', '状态')->using(User::Status_arr)->label(User::Status_label);
            $grid->column('created_at','注册日期');
            $grid->quickSearch(['id', 'name'])->placeholder('用户ID,真实姓名');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            //$grid->disableActions();
            $grid->disableRowSelector();
            //$grid->export();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                //
                $actions->disableView();
                if(!empty($actions->row->card_code)){
                    $form = Form::make(new User());
                    //$form->confirm('确认现在重置密码？');
                    $form->action('user-handle/card-unavailable');
                    $form->html('挂失会员卡后台，将删除用户微信卡包会员卡');
                    //$form->text('username', '账号')->value($actions->row->username)->disable()->required();
                    $form->hidden('user_id')->value($actions->row->id)->required();
                    $form->hidden('hotel_id')->value($actions->row->hotel_id);
                    $form->hidden('card_code')->value($actions->row->card_code);
                    $form->disableEditingCheck();
                    $form->disableCreatingCheck();
                    $form->disableViewCheck();
                    $modal = Modal::make()
                        ->title('挂失会员卡')
                        ->body($form)
                        ->button('<i class="feather icon-delete tips" data-title="挂失会员卡"></i>');
                    //$actions->append($modal);
                }

            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id','用户ID')->width(3);
                $filter->equal('card_code','会员卡号')->width(3);
            });
        });

        return  Card::make('',$grid);

    }

    // 会员用户
    protected function tab2() {
        $grid =  Grid::make(User::with('level'), function (Grid $grid) {
            $grid->model()->where([['hotel_id' ,'=', Admin::user()->hotel_id],['booking_num','>=',1]])->orderBy('id', 'DESC');
            $grid->column('avatar','头像')->image('', '44');
            $grid->column('nick_name','昵称');
            $grid->column('zs_name','真实姓名');
            $grid->column('card_code');
            $grid->column('card_code', '会员卡号');
            $grid->column('booking_num', '订房次数');
            $grid->column('point', '积分');
            $grid->column('balance', '余额');
            $grid->column('created_at','注册日期');
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                //
                $actions->disableView();
            });
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });

        return  Card::make('',$grid);
   }

   // 用户等级
    protected function tab3(){
        $grid =  Grid::make(new UserLevel(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('level_logo','等级图标')->image('', '44');
            $grid->column('level_name','等级名称');
            $grid->column('level_desc','等级描述');
            $grid->column('min_booking_num','该等级所需的最小积分');
            $grid->column('max_booking_num','该等级所需的最大积分');
            $grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->enableDialogCreate(); // 打开弹窗创建
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

        return  Card::make('',$grid);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id) {
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('uid');
            $show->field('uname');
            $show->field('phone');
            $show->field('name');
            $show->field('id_card');
            $show->field('buy_time');
            $show->field('total_cost');
            $show->field('total_num');
            $show->field('follow_time');
            $show->field('deadline_time');
            $show->field('type');
            $show->field('uniacid');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('create_time');
            $show->field('update_time');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form =  Form::make(new User(), function (Form $form) {
            $form->title('编辑用户信息');
            $form->display('id');
            $form->photo('avatar','头像')
                ->width(6)
                ->disk('hotel_'.Admin::user()->hotel_id)
                ->accept('jpg,png,jpeg')
                ->help('图标尺寸:144*144,格式：jpg,png,jpeg')
                ->nametype('datetime')
                ->saveFullUrl(true)
                ->remove(true);
            $form->text('nick_name','昵称');
            $form->text('zs_name','真实姓名');
            $form->text('idCard','身份证号');
            $form->text('phone','手机号');
            $form->text('email','邮箱');
            $form->disableResetButton();

        });

        return $form;
    }
}
