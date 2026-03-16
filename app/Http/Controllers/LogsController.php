<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Apilog;

class LogsController extends Controller
{

    public function list(){
        return view('pages.logs.list');
    }

    /**
     * @desc 获取日志列表
     * @param Request $request
     * author eRic
     * dateTime 2021-12-29 15:50
     */
    public function index(Request $request){
        if(!\Auth::guard('admin')->check()){
            abort(403,'你没有权限');
        }
        $pagesize      = empty($request->pagesize) || $request->pagesize > 100 ? 20 : $request->pagesize;
        $where[] = ['id','>',1];
        if(!empty($request->user_id)){
            $where[] = ['user_id','=',$request->apiname];
        }
        if(!empty($request->apiname)){
            $where[] = ['apiname','like','%'.$request->apiname.'%'];
        }
        if(!empty($request->pageurl)){
            $where[] = ['pageurl','like','%'.$request->pageurl.'%'];
        }
        if(!empty($request->request_name)){
            $where[] = ['request','like','%'.$request->request_name.'%'];
        }
        if(!empty($request->result)){
            $where[] = ['result','like','%'.$request->result.'%'];
        }
        if (!empty($request->start_time)) {
            $where[] = ['created_at', '>=', $request->start_time . ' 00:00:00'];
        }
        if (!empty($request->end_time)) {
            $where[] = ['created_at', '<=', $request->end_time . ' 23:59:59'];
        }
        $list = Apilog::where($where)
            ->where('apiname','<>','admin/login')
            ->with('users')
            ->orderBy('id','DESC')
            ->paginate($pagesize);
        $data['list'] = $list;
        return view('admin.logs.index',$data);
    }
    /**
     * @desc 查看错误日志
     * author eRic
     * dateTime 2020/8/3 9:57 上午
     */
    public function info(Request $request){
        $logid = $request->get('logid','');
        $info = Apilog::where('id',$logid)->first();
        echo "<pre>";
        print_r(collect($info)->toArray());
        echo "</pre>";
        exit;
        return view('pages.logs.info',compact('info'));
    }

}
