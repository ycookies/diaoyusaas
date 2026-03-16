<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dcat\Admin\Admin;
use App\Merchant\Models\MerchantUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Form;
use Dcat\Admin\Http\JsonResponse;

class MerchantUserHandleController extends Controller {

    // 重置登陆密码
    public function resetPassword(Request $request) {
        $user_id = $request->get('user_id');
        if(empty($user_id)){
            return (new WidgetForm())->response()->error('用户不存在,请检查');
        }
        $newpsd = Str::random(8);
        $user = MerchantUser::find($user_id);
        $user->password = Hash::make($newpsd);
        $user->setRememberToken(Str::random(60));
        $user->save();
        //info(['user_id'=> $user_id,'newpsd'=>$newpsd]);
        return (new WidgetForm())->response()->alert(true)->success($newpsd)->detail('重置的登陆密码');
    }

    // 编辑账户信息
    public function editAccount(Content $content){
        $request = Request();
        $form = Form::make(new MerchantUser())->edit($request->id);
        $form->action('/merchant-user-handle/editAccountSave');
        $form->confirm('确认已填写好,现在保存吗？');
        $form->hidden('id')->value($request->id);
        $form->date('expired_at','账户过期时间')->required();
        $form->checkbox('module_permissions','权限设置')->options(MerchantUser::Permissions_arr)->canCheckAll()->required();
        $form->switch('is_show_copyright','小程序展示版权')
            ->help('在酒店小程序里面展示【融 宝 易 住 | 提供技术支持】');
        return $content
            ->header('编辑账户信息')
            ->description('全部')
            ->breadcrumb(['text' => '编辑账户信息', 'uri' => ''])
            ->body($form);
    }

    // 保存 编辑账户信息
    public function editAccountSave(Request $request){
        $id = $request->get('id');
        if(empty($id)){
            return (new WidgetForm())->response()->error('酒店账号ID 不能为空');
        }
        $muser = MerchantUser::find($id);
        if(empty($muser->id)){
            return (new WidgetForm())->response()->error('酒店账号信息未找到');
        }
        $module_permissions = $request->module_permissions;

        if(is_array($request->module_permissions)){
            $module_permissions = json_encode(array_filter($module_permissions),JSON_UNESCAPED_UNICODE);
        }
        /*$muser->expired_at =$request->expired_at;
        $muser->module_permissions = '[]';
        $muser->is_show_copyright = $request->is_show_copyright;
        $muser->save();*/
        $updata = [
            'expired_at' => $request->expired_at,
            'module_permissions' => $module_permissions,
            'is_show_copyright' => $request->is_show_copyright,
        ];
        MerchantUser::where(['id'=> $id])->update($updata);
        $insdata = [
          'is_show_copyright'  =>  $request->is_show_copyright
        ];
        $sts = \App\Models\Hotel\HotelSetting::createRow($insdata,$muser->hotel_id);

        return JsonResponse::make()->success('保存更新成功')->refresh();
    }
}
