<?php

namespace App\Merchant\Controllers\AdminUser;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Http\Repositories\Role;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Tree;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
class AdRoleController extends AdminController
{
    public function index(Content $content) {
        return $content
            ->header('角色')
            ->description('全部')
            ->breadcrumb(['text' => '角色', 'uri' => ''])
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(new Role(), function (Grid $grid) {
            if(Admin::user()->id != 1){
                $grid->model()->where([['id','>',2]]);
            }
            $grid->column('id', 'ID')->sortable();
            $grid->column('slug')->label('primary');
            $grid->column('name','角色名');
            //$grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->disableEditButton();
            if(Admin::user()->id != 1){
                $grid->disableCreateButton();
                $grid->disableDeleteButton();
                $grid->disableBatchActions();
                $grid->disableBatchDelete();
            }
            $grid->showQuickEditButton();
            $grid->quickSearch(['id', 'name', 'slug']);
            $grid->enableDialogCreate();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $roleModel = config('admin.database.roles_model');
                if ($roleModel::isAdministrator($actions->row->slug)) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                    $actions->disableQuickEdit();
                    $actions->disableView();
                }
                if(Admin::user()->id != 1){
                    $actions->disableDelete();
                    $actions->disableEdit();
                    $actions->disableQuickEdit();
                    $actions->disableView();
                }
            });
        });
    }
}
