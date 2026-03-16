<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cgcms\Archive;
use App\Models\Cgcms\Arctype;
use App\Models\Cgcms\Ad;
use App\Models\Cgcms\Partner;
class IndexController extends BaseController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $Request)
    {

        $new_list = Archive::where([['typeid','=',5]])->orderBy('id','DESC')->limit(5)->get();
        $data['new_list'] = $new_list;
        $banner_new = Archive::where([['typeid','=',5]])->orderBy('id','DESC')->limit(1)->first();
        $data['banner_new'] = $banner_new;
        $web_style = config('admin.web_style');
        $data['slides'] = Ad::where(['status'=>1])->get();
        $data['partner'] = Partner::all();
        return view('style1.pc.index',$data);
    }

    /**
     * @desc 文章模型列表
     */
    public function listArticle(Request $Request,$typeid){
        $info = Arctype::find($typeid);
        $list = Archive::where(['typeid'=> $typeid])
            ->orderBy('id','DESC')
            ->paginate(10);
        $sub_typelist = [];
        $parent_info = [];
        if($info->parent_id == 0){
            $sub_typelist = Arctype::where(['parent_id'=>$typeid])->get();
        }else{
            $sub_typelist = Arctype::where(['parent_id'=>$info->parent_id])->get();
            $info = Arctype::find($info->parent_id);
        }

        $data['list'] = $list;
        $data['info'] = $info;
        $data['sub_typelist'] = $sub_typelist;

        $channel_list = Arctype::$channeltype_list;
        $channel_info = $channel_list[$info->current_channel];
        // 模板名
        $view_name = 'style1.pc.lists_'.$channel_info['nid'];

        return view($view_name,$data);
    }

    /**
     * @desc 文章模型 详情页
     */
    public function articleView(Request $Request,$id){

        $info = Archive::where(['id'=> $id])->first();

        $randlist = $list = Archive::where([['id','>',1]])
            ->orderBy('id','DESC')
            ->paginate(10);

        $data['info'] = $info;
        $data['randlist'] = $randlist;

        $channel_list = Arctype::$channeltype_list;
        $channel_info = $channel_list[$info->channel];
        // 模板名
        $view_name = 'style1.pc.view_'.$channel_info['nid'];
        return view($view_name,$data);
    }
}
