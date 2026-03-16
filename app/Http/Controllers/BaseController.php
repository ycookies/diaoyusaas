<?php

namespace App\Http\Controllers;

use App\Models\Cgcms\Arctype;
use Illuminate\Support\Facades\View;
use App\Models\Cgcms\Configs as ConfigModel;

class BaseController extends Controller {
    public function __construct() {
        $path = \Request::path();
        $index_active = 'active';
        $where             = [];
        $where[]           = ['parent_id', '=', 0];
        $where[]           = ['is_hidden', '=', 0];
        $where[]           = ['status', '=', 1];
        $list              = Arctype::where($where)->select('id', 'parent_id', 'title','typelink')->orderBy('sort_order','ASC')->get();
        foreach ($list as $keys => &$items) {
            $where1 = [];
            $where1[]          = ['parent_id', '=', $items->id];
            $where1[]        = ['is_hidden', '=', 0];
            $where1[]        = ['status', '=', 1];
            $sub_item = Arctype::where($where1)->orderBy('sort_order','ASC')->get();
            $items->sub_item = $sub_item;
            $a_link = '';
            if($sub_item->isEmpty()){
                if($items->is_part == 1){
                    $a_link = $items->typelink;
                }else{
                    $a_link = url('listArticle').'/'.$items->id;
                }
            }else{
                $a_link = url('listArticle').'/'.$items->id;
            }
            $items->a_link = $a_link;
            $is_active = '';
            if($path == 'listArticle/'.$items->id){
                $is_active = 'active';
                $index_active = '';
            }
            $items->is_active = $is_active;
        }

        $data['menu_list'] = $list;
        $web_base = ConfigModel::getlists([],'web_base');
        View::share('index_active', $index_active);
        View::share('menu_list', $data['menu_list']);
        View::share('web_base', $web_base);
    }
}
