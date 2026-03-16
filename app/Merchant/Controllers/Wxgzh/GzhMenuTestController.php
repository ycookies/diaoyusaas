<?php

namespace App\Merchant\Controllers\Wxgzh;

use App\Http\Controllers\Controller;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Tree;
// 微信公众号 菜单
class GzhMenuTestController extends Controller {

    public $oauth;

    /**
     * @desc
     * @param Content $content
     * author eRic
     * dateTime 2025-06-17 15:20
     */
    public function index(Content $content){
        $grid = '';
        $wxOpen = app('wechat.open');
        $menuinfo = $wxOpen->hotelWxgzh(Admin::user()->hotel_id)->menu->current();

        return $content
            ->header('公众号自定义菜单')
            ->description('')
            ->breadcrumb(['text' => '公众号自定义菜单', 'url' => ''])
            ->body(function (Row $row) {
                $row->column(7, $this->treeView()->render());

                $row->column(5, function (Column $column) {

                });
            });

    }

    protected function treeView()
    {
        //$menuModel = config('admin.database.menu_model');

        return new Tree(null, function (Tree $tree) {
            $tree->model()->setData([]);
            $tree->disableCreateButton();
            $tree->disableQuickCreateButton();
            $tree->disableEditButton();
            $tree->maxDepth(3);

            $tree->actions(function (Tree\Actions $actions) {
                if ($actions->getRow()->extension) {
                    $actions->disableDelete();
                }

                //$actions->prepend(new Show());
            });

            $tree->branch(function ($branch) {
                $payload = "<i class='fa {$branch['icon']}'></i>&nbsp;<strong>{$branch['title']}</strong>";

                if (! isset($branch['children'])) {
                    if (url()->isValidUrl($branch['uri'])) {
                        $uri = $branch['uri'];
                    } else {
                        $uri = admin_base_path($branch['uri']);
                    }

                    $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"$uri\" class=\"dd-nodrag\">$uri</a>";
                }

                return $payload;
            });
        });
    }
}
