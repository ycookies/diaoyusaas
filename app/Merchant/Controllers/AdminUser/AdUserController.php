<?php

namespace App\Merchant\Controllers\AdminUser;

use App\Merchant\Models\Administrator as AdministratorModel;
use App\Merchant\Repositories\Administrator;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Tree;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Modal;

class  AdUserController extends AdminController {

    public function index(Content $content) {
        return $content
            ->header('管理人员')
            ->description('全部')
            ->breadcrumb(['text' => '管理人员', 'uri' => ''])
            ->body($this->grid());
    }

    protected function grid() {
        $grid =  Grid::make(Administrator::with(['roles']), function (Grid $grid) {
            if(Admin::user()->id == 1){
                $grid->model()->orderBy('id', 'DESC');
            }else{
                $grid->model()->where([['parent_id', '=', Admin::user()->id]])->orderBy('id', 'DESC');
            }
            $grid->column('id', 'ID');
            $grid->column('roles')->pluck('name')->label('primary', 3);
            /*if (config('merchant.permission.enable')) {
                $permissionModel = config('merchant.database.permissions_model');
                $roleModel = config('merchant.database.roles_model');
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
            $grid->column('username');
            $grid->column('name');
            $grid->column('phone', '电话');
            $grid->column('is_wx_openid', '绑定微信')->using([0 => '否', 1 => '是']);
            $grid->column('work_status', '工作状态')->using([0 => '离职', 1 => '正常']);
            $grid->column('is_active', '激活状态')->using([0 => '否', 1 => '是']);
            //$grid->column('balance','账户余额');
            //$grid->column('point','账户积分');
            //$grid->column('user_status','使用状态')->help('可禁止登陆使用')->switch();
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

        $mobile_link = env('APP_URL').'/run/home';

        $modal = Modal::make()
            ->title('运营人员移动管理端')
            ->body('<div style="text-align: center"><h3>手机扫码查看</h3> '.\QrCode::generate($mobile_link).' </div>')
            ->button('查看二维码');


        // 加上提示信息
        $htmll = <<<HTML
<ul>
    <li>1.运营人员:可以使用手机移动端来确认订单，查看每日统计等操作. <a class="clipboard-txt" data-clipboard-text='$mobile_link' href="javascript:void(0);" >   复制链接 <i class='feather icon-copy'></i></a> $modal</li>
</ul>
HTML;

        $alert = Alert::make($htmll, '提示:');

        return $alert->info()->removable().$grid;
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
            $form->hidden('parent_id')->value(Admin::user()->id);
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('username', trans('admin.username'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$userTable}"])
                ->updateRules(['required', "unique:{$connection}.{$userTable},username,$id"]);
            $form->text('name', trans('admin.name'))->required();
            $form->image('avatar', trans('admin.avatar'))->default('https://ask.dsxia.cn/img/toux1.png')->url('/upload/imgs')->saveFullUrl()->removable(false)->autoUpload();

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

            if (config('merchant.permission.enable')) {
                $form->multipleSelect('roles', trans('admin.roles'))
                    ->options(function () {
                        $roleModel = config('merchant.database.roles_model');
                        return $roleModel::whereNotIn('id',[1,2])->pluck('name', 'id');
                    })->customFormat(function ($v) {
                        return array_column($v, 'id');
                    })->default(1);
            }
            //$form->hidden('roles')->value(1);
            $form->text('phone', '手机号')->required();
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
                $seller = Admin::user();
                $data   = \App\Models\Hotel\Room::where(['seller_id' => 0])->get();
                foreach ($data as $key => $items) {
                    $items->seller_id = $seller->id;
                    $insdata          = collect($items)->toArray();
                    unset($insdata['id']);
                    unset($insdata['created_at']);
                    unset($insdata['created_at']);
                    \App\Models\Hotel\Room::create($insdata);
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
