<?php

namespace App\Merchant\Controllers;

use Dcat\Admin\Http\Controllers\AuthController as BaseAuthController;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Repositories\Administrator;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Traits\HasFormResponse;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\SubscribeGzhForm;

class AuthController extends BaseAuthController
{
    protected $view = 'merchant.login';

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        return new Form(new Administrator(), function (Form $form) {
            $form->action(admin_url('auth/setting'));

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();

            $form->tools(function (Form\Tools $tools) {
                $tools->disableView();
                $tools->disableDelete();
            });

            $form->display('username', trans('admin.username'));
            $form->text('name', trans('admin.name'))->required();

            $weChatFlag = 'merchant_'.$form->model()->id; //参数
            $modal = Modal::make()
                ->centered() // 设置弹窗垂直居中
                ->title('扫码 关注公众号 ')
                ->body(SubscribeGzhForm::make()->payload(['weChatFlag'=> $weChatFlag]))
                ->button('<span class="text-danger tips" data-title="请点击"> 扫码关注公众号 </span> &nbsp;&nbsp;');

            $form->text('wx_openid', '关注公众号')->readOnly()
                ->placeholder('请扫码关注公众号')
                ->help('点击 '.$modal.' 关注['.env('APP_NAME').']公众号');
            $form->image('avatar', trans('admin.avatar'))->autoUpload();

            $form->password('old_password', trans('admin.old_password'));

            $form->password('password', trans('admin.password'))
                ->minLength(5)
                ->maxLength(20)
                ->customFormat(function ($v) {
                    if ($v == $this->password) {
                        return;
                    }

                    return $v;
                });
            $form->password('password_confirmation', trans('admin.password_confirmation'))->same('password');

            $form->ignore(['password_confirmation', 'old_password']);

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }

                if (! $form->password) {
                    $form->deleteInput('password');
                }
            });

            $form->saved(function (Form $form) {
                return $form
                    ->response()
                    ->success(trans('admin.update_succeeded'))
                    ->redirect('auth/setting');
            });
        });
    }

    protected function username() {
        return 'phone';
    }
}
