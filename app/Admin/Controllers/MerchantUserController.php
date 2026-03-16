<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\HotelSettingForm;
use App\Merchant\Models\Administrator as AdministratorModel;
use App\Merchant\Repositories\Administrator;
use App\Models\MerchantUser as Muser;
use App\Merchant\Models\MerchantUser;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tree;
use Illuminate\Support\Facades\URL;

class MerchantUserController extends AdminController {
    public function index(Content $content) {
        //$this->config = WxappConfig::getConfig(143);
        return $content
            ->header('酒店账户管理')
            ->description('全部')
            ->breadcrumb(['text' => '酒店账户管理', 'uri' => ''])
            ->body($this->grid());
    }

    protected function grid() {
        return Grid::make(Administrator::with('hotel'), function (Grid $grid) {
            $grid->model()->where([['id', '<>', 1], ['hotel_id', '<>', '']])->orderBy('id', 'DESC');
            $grid->id('ID')->bold()->sortable();
            $grid->name->bold()->tree(); // 开启树状表格功能
            $grid->column('username', '账号/手机')->display(function () {
                return '账号:' . $this->username . '<br/>手机:' . $this->phone;
            });
            //$grid->column('phone','手机号码');
            //$grid->column('balance','账户余额');
            //$grid->column('point','账户积分');
            $grid->column('is_wxgzh_subscribe', '关注公众号')->bool();
            $grid->column('hotel.name', '绑定酒店');
            $grid->column('user_status', '使用状态')->help('可禁止登陆使用')->switch();
            $grid->column('expired_at', '有效期')->help('有效期过后,小程序将进入打烊状态');
            $grid->column('created_at', '入驻日期')->display(function () {
                return date('Y-m-d', strtotime($this->created_at));
            });
            $grid->quickSearch(['id', 'name', 'username', 'phone'])->placeholder('用户ID,用户名,账号,手机号码');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                //if ($actions->getKey() == AdministratorModel::DEFAULT_ID) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();
                // 编辑账户信息
                $actions->append('<a class="tips" target="_blank" data-title="编辑" href="' . admin_url('/merchant-user-handle/editAccount?id=' . $actions->row->id) . '"> <i class="feather icon-edit"></i></a>  &nbsp;');
                $form = Form::make(new Administrator());
                $form->confirm('确认现在重置密码？');
                $form->action('merchant-user-handle/resetPassword');
                $form->html('重置的登陆密码为随机密码');
                $form->text('username', '账号')->value($actions->row->username)->disable()->required();
                $form->hidden('user_id')->value($actions->row->id)->required();
                $form->disableEditingCheck();
                $form->disableCreatingCheck();
                $form->disableViewCheck();
                if ($actions->row->parent_id == 0) {
                    $modal = Modal::make()
                        ->lg()
                        ->title('配置分账参数')
                        ->body(HotelSettingForm::make()->payload($actions->row->toArray()))
                        ->button('<i class="feather icon-shuffle tips" data-title="配置分账参数"></i> &nbsp;&nbsp;');
                    $actions->append($modal);
                }

                $modal = Modal::make()
                    ->lg()
                    ->title('重置登陆密码')
                    ->body($form)
                    ->button('<i class="fa fa-key tips" data-title="重置登陆密码"></i>');
                $actions->append($modal);
                $loginurl = URL::signedRoute('autologin', ['user' => Muser::where(['id' => $actions->row->id])->first()], now()->addMinutes(1), true);
                $actions->append('<a class="tips" target="_blank" data-title="登陆商户后台" href="' . $loginurl . '" > <i class="feather icon-log-in"></i></a>');

            });
            /*$grid->actions(function (Grid\Displayers\Actions $actions) {

                //signedRoute 函数的后台两个参数：过期时间【单位分钟】，指定生成的 URL 是否为绝对 URL。如果设置为 true，则生成的 URL 包含完整的域名和协议
                $loginurl = URL::signedRoute('autologin', ['user' => Muser::where(['id'=>$actions->row->id])->first()],now()->addMinutes(1),true);
                $actions->append('<a class="tips" target="_blank" data-title="登陆商户后台" href="'.$loginurl.'" > <i class="feather icon-log-in"></i></a>');
            });*/
            $grid->disableRowSelector(); // 禁止行选择
            $grid->disableBatchActions();// 禁止批量操作
            $grid->enableDialogCreate();
            $grid->disableBatchDelete();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('id');
                $filter->like('name');
                $filter->like('username');
            });

        });
    }


    protected function grid1() {
        return Grid::make(Administrator::with(['roles']), function (Grid $grid) {
            $grid->model()->where([['id', '<>', 1]])->orderBy('id', 'DESC');
            $grid->column('id', 'ID')->sortable();
            $grid->column('username');
            $grid->column('name');
            $grid->column('phone');
            $grid->column('roles', '角色')->pluck('name')->label('primary', 3);
            /*if (config('lawyer.permission.enable')) {
                $grid->column('roles')->pluck('name')->label('primary', 3);

                $permissionModel = config('lawyer.database.permissions_model');
                $roleModel = config('lawyer.database.roles_model');
                $nodes = (new $permissionModel())->allNodes();
                $grid->column('permissions')
                    ->if(function () {
                        return ! $this->roles->isEmpty();
                    })
                    ->showTreeInDialog(function (Grid\Displayers\DialogTree $tree) use (&$nodes, $roleModel) {
                        $tree->nodes($nodes);

                        foreach (array_column($this->roles->toArray(), 'slug') as $slug) {
                            if ($roleModel::isAdministrator($slug)) {
                                $tree->checkAll();
                            }
                        }
                    })
                    ->else()
                    ->display('');
            }*/
            $grid->column('balance', '账户余额');
            $grid->column('point', '账户积分');
            $grid->column('user_status', '使用状态')->help('可禁止登陆使用')->switch();
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();

            $grid->quickSearch(['id', 'name', 'username']);

            $grid->showQuickEditButton();
            $grid->enableDialogCreate();
            $grid->showColumnSelector();
            $grid->disableEditButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == AdministratorModel::DEFAULT_ID) {
                    $actions->disableDelete();
                }
            });
        });
    }

    protected function detail($id) {
        return Show::make($id, Administrator::with(['roles']), function (Show $show) {
            $show->field('id');
            $show->field('username');
            $show->field('name');

            $show->field('avatar', __('merchant.avatar'))->image();

            if (config('merchant.permission.enable')) {
                $show->field('roles')->as(function ($roles) {
                    if (!$roles) {
                        return;
                    }

                    return collect($roles)->pluck('name');
                })->label();

                $show->field('permissions')->unescape()->as(function () {
                    $roles = $this->roles->toArray();

                    $permissionModel = config('merchant.database.permissions_model');
                    $roleModel       = config('merchant.database.roles_model');
                    $permissionModel = new $permissionModel();
                    $nodes           = $permissionModel->allNodes();

                    $tree = Tree::make($nodes);

                    $isAdministrator = false;
                    foreach (array_column($roles, 'slug') as $slug) {
                        if ($roleModel::isAdministrator($slug)) {
                            $tree->checkAll();
                            $isAdministrator = true;
                        }
                    }

                    if (!$isAdministrator) {
                        $keyName = $permissionModel->getKeyName();
                        $tree->check(
                            $roleModel::getPermissionId(array_column($roles, $keyName))->flatten()
                        );
                    }

                    return $tree->render();
                });
            }

            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    public function form() {
        return Form::make(Administrator::with(['roles']), function (Form $form) {
            $userTable = config('merchant.database.users_table');

            $connection = config('merchant.database.connection');

            $id = $form->getKey();

            $form->display('id', 'ID');

            $form->text('username', trans('admin.username'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$userTable}"])
                ->updateRules(['required', "unique:{$connection}.{$userTable},username,$id"]);
            $form->text('name', trans('admin.name'))->required();
            $form->image('avatar', trans('admin.avatar'))->default(env('APP_URL').'/img/toux1.png')->url('/upload/imgs')->saveFullUrl()->removable(false)->autoUpload();

            if ($id) {
                $form->password('password', trans('admin.password'))
                    ->minLength(5)
                    ->maxLength(20)
                    ->customFormat(function () {
                        return '';
                    });
            } else {
                $form->password('password', trans('admin.password'))
                    ->required()
                    ->minLength(5)
                    ->maxLength(20);
            }

            $form->password('password_confirmation', trans('admin.password_confirmation'))->same('password');

            $form->ignore(['password_confirmation']);

            /*if (config('merchant.permission.enable')) {
                $form->multipleSelect('roles', trans('admin.roles'))
                    ->options(function () {
                        $roleModel = config('merchant.database.roles_model');
                        return $roleModel::all()->pluck('name', 'id');
                    })->customFormat(function ($v) {
                        return array_column($v, 'id');
                    })->default(2);
            }*/
            $form->hidden('roles')->value(2);
            $form->text('phone', '手机号')->required();
            $form->date('expired_at', '账户过期时间')->required();
            $form->checkbox('module_permissions','权限设置')->options(MerchantUser::Permissions_arr)->canCheckAll()->required();
            $form->switch('is_show_copyright', '小程序展示版权')
                ->help('在酒店小程序里面展示【融 宝 易 住 | 提供技术支持】');
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            if ($id == AdministratorModel::DEFAULT_ID) {
                $form->disableDeleteButton();
            }

        })->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if (!$form->password) {
                $form->deleteInput('password');
            }
        })->saved(function (Form $form, $result) {
            // 判断是否是新增操作
            // 生成初始化信息
            if ($form->isCreating()) {
                $newId = $result;
                \DB::connection('hotel')->beginTransaction();
                try {
                    $seller = Admin::user();
                    // 自增ID

                    // 生成酒店信息
                    //$hotelinfo = \App\Models\Hotel\Hotel::where(['id'=> 1])->first();
                    $hoteobj                    = \DB::connection('hotel')->table('hotel')->where(['id' => 1])->first();
                    $hoteldata                  = collect($hoteobj)->toArray();
                    $hoteldata['hotel_user_id'] = $newId;
                    $hoteldata['shop_open'] = 1;
                    unset($hoteldata['id']);
                    unset($hoteldata['created_at']);

                    $hotel_id = \DB::connection('hotel')->table('hotel')->insertGetId($hoteldata);
                    //$newhotel = \App\Models\Hotel\Hotel::create($hoteldata);

                    // 生成酒店房间
                    //$data = \App\Models\Hotel\Room::where(['hotel_id'=> 1])->get();
                    $data = \DB::connection('hotel')->table('room')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('room')->insertGetId($insdata);
                    }
                    // 生成轮播图片
                    $data = \DB::connection('hotel')->table('banner')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('banner')->insertGetId($insdata);
                    }

                    // 生成房间设施配置
                    $data = \DB::connection('hotel')->table('room_sheshi_config')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('room_sheshi_config')->insertGetId($insdata);
                    }
                    // 生成内容分类
                    $data = \DB::connection('hotel')->table('article_types')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('article_types')->insertGetId($insdata);
                    }

                    // 生成内容列表
                    $data = \DB::connection('hotel')->table('article_types')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('article_types')->insertGetId($insdata);
                    }
                    $data = \DB::connection('hotel')->table('articles')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('articles')->insertGetId($insdata);
                    }

                    // 帮助 协议模板
                    $data = \DB::connection('hotel')->table('help_types')->where(['hotel_id' => 1])->get();
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('help_types')->insertGetId($insdata);
                    }

                    $data = \DB::connection('hotel')->table('help')->where(['hotel_id' => 1])->get();
                    $type_id = \DB::connection('hotel')->table('help_types')->where(['hotel_id'=>$hotel_id,'name'=>'政策协议'])->value('id');
                    foreach ($data as $key => $items) {
                        $items->hotel_id = $hotel_id;
                        $items->type_id = $type_id;
                        $insdata         = collect($items)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('help')->insertGetId($insdata);
                    }

                    // 生成会员等级配置
                    $data2 = \DB::connection('hotel')->table('user_levels')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('user_levels')->insertGetId($insdata);
                    }
                    // VIP会员配置
                    $data2 = \DB::connection('hotel')->table('member_vip_set')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('member_vip_set')->insertGetId($insdata);
                    }

                    // 广告配置
                    $data2 = \DB::connection('hotel')->table('ad')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('ad')->insertGetId($insdata);
                    }

                    // 生成优惠券
                    $data2 = \DB::connection('hotel')->table('coupons')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $itemsa->user_id = $newId;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('coupons')->insertGetId($insdata);
                    }
                    // 生成微信会员开卡配置项
                    $data2 = \DB::connection('hotel')->table('hotel_wx_card_tpl')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('hotel_wx_card_tpl')->insertGetId($insdata);
                    }
                    // 点金计划
                    $data2 = \DB::connection('hotel')->table('hotel_gold_plan')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('hotel_gold_plan')->insertGetId($insdata);
                    }
                    // 生成基础配置项
                    $data2 = \DB::connection('hotel')->table('hotel_setting')->where(['hotel_id' => 1])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('hotel_setting')->insertGetId($insdata);
                    }

                    // 小程序页面管理
                    $data2 = \DB::connection('hotel')->table('miniprogram_pages')->where(['hotel_id' => 225])->get();
                    foreach ($data2 as $key => $itemsa) {
                        $itemsa->hotel_id = $hotel_id;
                        $insdata          = collect($itemsa)->toArray();
                        unset($insdata['id']);
                        unset($insdata['created_at']);
                        \DB::connection('hotel')->table('miniprogram_pages')->insertGetId($insdata);
                    }

                    $model = Muser::find($newId);
                    $model->hotel_id = $hotel_id;
                    $model->save();
                    \DB::connection('hotel')->commit();
                    return $form->response()->success('创建成功')->refresh();
                } catch (\Error $error) {
                    \DB::connection('hotel')->rollBack();
                    Muser::where(['id'=>$newId])->delete();
                    return $form->response()->error('创建出错:'.$error->getMessage());
                } catch (\Exception $exception) {
                    \DB::connection('hotel')->rollBack();
                    Muser::where(['id'=>$newId])->delete();
                    return $form->response()->error('创建出错'.$exception->getMessage());
                }
            }
        });
    }

    public function destroy($id) {
        if (in_array(AdministratorModel::DEFAULT_ID, Helper::array($id))) {
            Permission::error();
        }

        return parent::destroy($id);
    }
}
