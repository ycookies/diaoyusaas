<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Admin\Controllers;

use App\Admin\Actions\Form\WxopenViewPermission;
use App\Models\Hotel\WxopenMiniProgramOauth;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Modal;

// 列表
class WxopenOauthController extends AdminController {

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('第三方app授权管理')
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
        return Grid::make(WxopenMiniProgramOauth::with('hotel'), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel.name', '酒店');
            $grid->column('app_name', '名称');
            $grid->column('app_type', '类型')
                ->using(WxopenMiniProgramOauth::App_type_arr)->label([
                    'minapp' => 'success',
                    'wxgzh'  => 'primary',
                ]);
            $grid->column('AuthorizerAppid', 'app_id');
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->disableRowSelector();
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $modal = Modal::make()
                    ->lg()
                    ->title('查看权限集')
                    ->body(WxopenViewPermission::make()->payload($actions->row->toArray()))
                    ->button('<span data-title="查看权限集">查看接口权限</span> &nbsp;&nbsp;');
                $actions->append($modal);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
        return Show::make($id, WxopenMiniProgramOauth::with('hotel'), function (Show $show) {
            $show->field('id');
            $show->field('hotel.name', '酒店名称');
            $show->field('app_name', '应用名称');
            $show->field('app_type', '类型')->using(WxopenMiniProgramOauth::App_type_arr);
            $show->field('AuthorizerAppid', 'app_id');
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
        return Form::make(new WxopenMiniProgramOauth(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('hotel_id');
            $form->text('minapp_id');
            $form->text('template_id');
            $form->text('user_version');
            $form->text('user_desc');
            $form->text('ext_json');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
