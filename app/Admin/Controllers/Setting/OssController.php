<?php

namespace App\Admin\Controllers\Setting;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Routing\Controller;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Hotel\Setting;
use Dcat\Admin\Widgets\Alert;

// 全局配置 上传设置
class OssController extends Controller
{
    public function index(Content $content){
        return $content
            ->header('上传设置')
            ->description('')
            ->breadcrumb(['text'=> '上传设置','uri'=> ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain(){
        $formdata = Setting::getlists([],'oss');
        $form = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('oss');
        //$form->html($alert = Alert::make('内容', '提示')->success());
        $form->radio('oss_ossType','默认上传方式')->options(['1'=>'腾讯云cos'])->value('1')->help('点击查看 <a href="#" target="_blank">配置教程</a>');
        $form->text('oss_bucket','存储空间名称 Bucket')->help('注意：这里不要填-appid，点击查看教程')->width(140)->required();
        $form->text('oss_region','所属地域 Region')->help('')->width(140)->placeholder('')->required();
        $form->text('oss_appId','AppId')->help('')->width(140)->placeholder('');
        $form->text('oss_secretId','SecretId')->help('<a href="https://img.mini.chongyeapp.com/images/appsecret.png" target="_blank">查看说明</a>')->width(140)->placeholder('');
        $form->password('oss_secretKey','SecretKey')->width(140)->placeholder('');
        $form->url('oss_domain','Domain')->help('请补全http:// 或 https://，例如：http://img.chongyeapp.com')->width(140)->placeholder('');
        $form->disableResetButton();
        $card =  Card::make('存储配置',$form);
        return $card;
    }
}
