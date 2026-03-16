<?php

namespace App\Admin\Controllers\Cgcms;

use App\Models\Cgcms\Configs as ConfigModel;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// 列表
class ConfigsController extends Controller {

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('基本信息')
            ->description('全部')
            ->breadcrumb(['text' => '基本信息', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $req  = Request()->all();
        $type = request('_t', 1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('官网信息', $this->tab0());
        /*$tab->add('资源上传', $this->tab1());
        $tab->add('微信支付服务商', $this->tab2());
        $tab->add('微信公众号', $this->tab3());
        $tab->add('微信小程序', $this->tab4());
        $tab->add('微信开放平台', $this->tab5());
        $tab->add('企业微信', $this->tab6());*/

        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    // 短信-邮件
    public function tab0() {
        $formdata = ConfigModel::getlists([
            'web_status',
            'web_name',
            'web_logo_local',
            'web_basehost',
            'web_copyright',
            'web_recordnum',
            'web_garecordnum',
            'web_title',
            'web_keywords',
            'web_description',
            'web_thirdcode_pc',
            'web_thirdcode_wap',
        ], 'web_base');
        $form1    = new WidgetsForm($formdata);
        $form1->confirm('确认已经填写完整了吗？');
        $form1->action('cgcms/configs/formsave');
        $form1->radio('web_status', '站点状态')->options(['1' => '开启', '0' => '关闭'])->value('1')->required();
        $form1->hidden('action_name')->value('web_base');
        $form1->hidden('is_refresh')->value(1);
        $form1->text('web_name', '网站名称')->required();
        $form1->image('web_logo_local', '电脑端LOGO')->width(3)->retainable()->removable(false)->url('uploads-web')->uniqueName()->autoUpload()->saveFullUrl();
        $form1->text('web_basehost', '网站网址')->required();
        $form1->text('web_copyright', '版权信息');
        $form1->text('web_recordnum', '备案号');
        $form1->text('web_garecordnum', '公安备案号');
        $form1->divider('首页信息');
        $form1->text('web_title', '首页标题');
        $form1->text('web_keywords', '首页关键词');
        $form1->textarea('web_description', '首页描述');
        $form1->divider('第三方代码');
        $form1->textarea('web_thirdcode_pc', '电脑端');
        $form1->textarea('web_thirdcode_wap', '手机端');
        $form1->disableResetButton();
        $card = Card::make('基本信息', $form1);
        return $card;
    }

    /**
     * @desc 参数存改主入口
     */
    public function formSave(Request $request) {
        $action_name = $request->get('action_name');
        $validator   = \Validator::make($request->all(), [
            'action_name' => 'required',
        ], [
            'action_name.required' => '操作项 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->all()[0]);
        }
        // 根据操作名不同 调用不用的函数做参数验证，存改
        switch ($action_name) {
            case 'web_base':
                return $this->web_base($request);
                break;

            case 'booking_switch':
                return $this->booking_switch($request);
                break;
            default:
                break;
        }
        return (new WidgetsForm())->response()->error('保存遇到问题,请检查');
    }

    //  网站基本信息
    public function web_base($request) {
        $validator = \Validator::make($request->all(), [
            'web_status'     => 'required',
            'web_name'       => 'required',
            //'web_logo_local' => 'required',
            'web_basehost'   => 'required|url',
            //'mail_from_test'    => 'nullable|email',
        ], [
            'web_status.required'     => '站店状态 不能为空',
            'web_name.required'       => '网站名称 不能为空',
            'web_logo_local.required' => '网站logo 不能为空',
            'web_basehost.required'   => '网站网址 不能为空',
            'web_basehost.url'        => '网站网址 格式不正确',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'web_status'        => $request->web_status,
            'web_name'          => $request->web_name,
            'web_logo_local'    => $request->web_logo_local,
            'web_basehost'      => $request->web_basehost,
            'web_copyright'     => $request->web_copyright,
            'web_recordnum'     => $request->web_recordnum,
            'web_garecordnum'   => $request->web_garecordnum,
            'web_title'         => $request->web_title,
            'web_keywords'      => $request->web_keywords,
            'web_description'   => $request->web_description,
            'web_thirdcode_pc'  => $request->web_thirdcode_pc,
            'web_thirdcode_wap' => $request->web_thirdcode_wap,
        ];
        $insdata = array_filter($insdata); // 过滤掉空值
        $sts     = ConfigModel::createRow($insdata, 'web_base');
        if ($request->is_refresh == '1') {
            return JsonResponse::make()->data($request->all())->success('成功！')->refresh();
        } else {
            return JsonResponse::make()->data($request->all())->success('成功！');
        }
    }
}
