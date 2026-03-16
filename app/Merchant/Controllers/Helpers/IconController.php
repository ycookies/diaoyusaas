<?php

namespace App\Merchant\Controllers\Helpers;

use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Routing\Controller;
use Dcat\Admin\Widgets\Alert;

class IconController extends Controller
{
    public function index(Content $content)
    {
        Admin::script(
            <<<JS
(function () {
   var clipboard = new ClipboardJS('.clipboard-txt');
   clipboard.on('success', function(e) {
       e.clearSelection();
       layer.msg('已复制');
    });
    clipboard.on('error', function(e) {
        e.clearSelection();
        layer.msg('复制内容失败');
    });
})()    
JS
        );
        Admin::style('.icon-list-demo div {
            cursor: pointer;
            line-height: 45px;
            white-space: nowrap;
            color: #75798B;
        }.icon-list-demo i {
            display: inline-block;
            font-size: 18px;
            margin: 0;
            vertical-align: middle;
            width: 40px;
        }');

        return $content->title('图标')->body(function (Row $row) {
            $htmll = <<<HTML
<ul>
    <li>直接 <b class="text-danger">点击</b> 图标就可以复制</li>
</ul>
HTML;

            $alert = Alert::make($htmll, '提示:');
            $tab = Tab::make()
                ->withCard()
                ->padding('20px')
                ->add(('Feather'), view('admin.helpers-tools.feather'))
                ->add(('Font Awesome'), view('admin.helpers-tools.font-awesome'));
            $row->column(12, $alert->success());
            $row->column(12, $tab);
        });
    }
}
