<?php

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tooltip;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\Cookie;
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
    //$form->disableResetButton();
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
    $grid->toolsWithOutline(false); //工具栏按钮默认显示 outline 模式
    $grid->filter(function (Grid\Filter $filter) {
        $filter->panel();
    });
    //$grid->model()->orderBy('id','DESC');
    //$grid->disableBatchDelete();
    //$grid->disableBatchActions();
    //$grid->toolsWithOutline(false);
});

Admin::style('.main-sidebar .nav-sidebar .nav-item>.nav-link {
    border-radius: .1rem;
}.form-group{margin-bottom:0.5rem}');
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

Admin::navbar(function (Navbar $navbar) {
    $menuModel = config('merchant.database.menu_model');
    $list = (new $menuModel())->where(['parent_id' => 0])->orderBy('order','ASC')->get();
    $menu_parent_id = !empty($_COOKIE['menu_parent_id'])? $_COOKIE['menu_parent_id']:'';
    $htmls = '';
    $is_active = false;
    foreach ($list as $key => $itemk) {
        $active  = '';
        if(!empty($menu_parent_id) && $menu_parent_id == $itemk['id'] && !$is_active){
            $active  = 'active';
            $is_active = true;
        }else{
            if($key == 0 && empty($menu_parent_id) && !$is_active){
                $active  = 'active';
                $is_active = true;
            }
        }

        $htmls .= '<a href="javascript:void(0);" class="nav-link nav-items '.$active.'" data-id="'.$itemk['id'].'">'.$itemk['title'].'</a>';
    }

    $navbar->left($htmls);
    //$navbar->right(\App\Admin\Actions\CacheClear::make()->render());
    $navbar->right(
        <<<HTML
        <a href="javascript:;"  data-check-screen="full" class="nav-link"><i class="feather icon-maximize" style="font-size: 1.2rem"></i></a>
HTML
    );
    // 下拉面板
    //$navbar->right(view('admin.navbar-1'));
    //$tab = Tab::make();
    //$navbar->right($tab->addLink('首页', env('APP_URL'),false));

    //$app_url = env('APP_URL');
    // 搜索框
    /**/
});


admin_inject_section(Admin::SECTION['LEFT_SIDEBAR_MENU'], function () {
    $menuModel = config('merchant.database.menu_model');
    $menulist = (new $menuModel())->allNodes()->toArray();
    //$menulist_all = Helper::buildNestedArray($menulist);
    $menu_parent_id = !empty($_COOKIE['menu_parent_id'])? $_COOKIE['menu_parent_id']:'';
    $html = '';
    foreach (Helper::buildNestedArray($menulist) as $item) {
        $html .= view('admin.partials.left_sidebar_menu', ['item' => &$item,'menu_parent_id'=> $menu_parent_id, 'builder' => Admin::menu()])->render();
    }
    return $html;
    //$item =  Admin::menu()->toHtml($menulist);
    //return view('admin.partials.left_sidebar_menu', ['menulist' => $menulist_all, 'builder' => Admin::menu()]);
});

/*// 动态创建文件系统配置
$hotel_id = Admin::user()->hotel_id;
Storage::extend('hotel', function ($app, $config) use ($hotel_id) {
    $config['driver'] = 'local';
    $config['root'] = public_path('uploads/merchant/'.$hotel_id); //storage_path('app/users/' . $hotel_id);
    $config['public'];
    $config['url'] = env('APP_URL').'/uploads/merchant/'.$hotel_id;
    return new \Illuminate\Filesystem\FilesystemAdapter(
        new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local($config['root'])
        )
    );
});*/

Tooltip::make('.tips')->purple();
