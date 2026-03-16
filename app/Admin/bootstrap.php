<?php

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Tooltip;

/**
 * Dcat-admin - admin builder based on Laravel.
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Show::resolving(function (Show $show){
    $show->disableListButton();
    $show->disableEditButton();
    $show->disableDeleteButton();
});

Form::resolving(function (Form $form){
    $form->disableHeader();
    $form->disableDeleteButton();
    $form->disableListButton();
    $form->disableResetButton();
    $form->disableViewCheck();
    $form->disableCreatingCheck();
    $form->disableEditingCheck();
});

Grid::resolving(function (Grid $grid) {
    //
    $grid->tableCollapse(false); //  table_collapse 模式
    $grid->withBorder(); // 让表格显示边框
    $grid->actions(function ($actions) {
        // 去掉删除
        //$actions->disableDelete();
        // 去掉编辑
        //$actions->disableEdit();
    });
    //
    $grid->toolsWithOutline(false); //工具栏按钮默认显示 outline 模式
    //$grid->model()->orderBy('id','DESC');
    //$grid->disableBatchDelete();
    //$grid->disableBatchActions();
    //$grid->toolsWithOutline(false);
});


Admin::style('.main-sidebar .nav-sidebar .nav-item>.nav-link {
    border-radius: .1rem;
}');
Admin::css('css/index.css?t='.time());
Admin::css('css/navbar.css?t='.time());
Admin::js('js/navbar.js?t='.time());
Admin::js('style1/Lib/clipboard/clipboard.min.js');
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
Tooltip::make('.tips')->purple();
Admin::navbar(function (Navbar $navbar) {
    $navbar->right(\App\Portal\Actions\CacheClear::make()->render());
    //$tab = Tab::make();
    //$navbar->right($tab->addLink('首页', env('APP_URL'),false));
    $app_url = env('APP_URL');
    // 搜索框
    $navbar->right(
        <<<HTML
        <a href="$app_url" target="_blank" class="nav-link">官网</a>
        <a href="{$app_url}/docs/showdoc/web/#/627898594" target="_blank" class="nav-link">系统文档</a>
        <a href="/merchant/auth/login" target="_blank" class="nav-link">商家后台</a>
HTML
    );
});
// Admin::menu(function (\Dcat\Admin\Layout\Menu $menu) {
//     $menu->add([
//         [
//             'id'        => 1,
//             'title'     => '用户中心',
//             'icon'      => 'feather icon-users',
//             'uri'       => '',
//             'parent_id' => 0,
//         ],
//         [
//             'id'        => 2,
//             'title'     => '用户管理',
//             'icon'      => 'feather icon-user',
//             'uri'       => 'member-user',
//             'parent_id' => 1,
//         ],
//         [
//             'id'        => 3,
//             'title'     => '网站配置',
//             'icon'      => 'feather icon-settings',
//             'uri'       => 'web-config',
//             'parent_id' => 0,
//         ],
//         [
//             'id'        => 4,
//             'title'     => '开放接口文档',
//             'icon'      => 'feather icon-layers',
//             'uri'       => '/openapi-docs',
//             'parent_id' => 0,
//         ]
// ]);
// });