<?php

namespace App\Merchant\Controllers\Mobile;

use App\Merchant\Repositories\Banner;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hotel\BookingOrder;
use App\Models\MerchantUser;
use App\Models\Hotel\Hotel;
//  酒店方 客房运营
class RunController extends Controller
{
    // 登陆
    public function runLogin(Request $request){
        if (\Auth::guard('run')->check()) {
            return redirect()->route('run.home');
        }
        if ($request->isMethod('POST')) {
            $request->validate(
                [
                    'username' => 'required',
                    'password' => 'required',
                ], [
                    'username.required' => '请填写用户名',
                    'password.required' => '请填写密码',
                ]
            );
            $login_type = 'email';
            if (is_numeric($request->username)) {
                $login_type = 'phone';
            } else {
                if (!filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                    $login_type = 'username';
                }
            }
            if (\Auth::guard('run')->attempt([$login_type => $request->username, 'password' => $request->password], $request->get('remember', ''))) {
                $user = MerchantUser::where([$login_type => $request->username])->first();
                \Auth::guard('run')->login($user);
                //\Auth::guard('merchant')->loginUsingId($user->id);
                return returnData(200, 1, [], '登陆成功');
            }

            /*$user = User::firstOrCreate([$login_type => $request->username], [
                'user_id'   => $userinfo['id'],
                'name'      => $userinfo['username'],
                'email'     => $request->username,
                'password'  => Hash::make($request->password),
                'api_token' => $res['token'],
                'image_url' => $userinfo['image_url'],
                'phone'     => $userinfo['phone'],
            ]);
            Auth::login($user, true);*/

            return returnData(403, 0, [], '账号或密码不正确');
        }

        return view('merchant.mobile.run.login');
    }

    // 退出登陆
    public function runLogout(Request $request){
        \Auth::guard('run')->logout();
        return redirect('run/login');
    }
    // 首页
    public function home(Request $request){
        if (!\Auth::guard('run')->check()) {
            $openid = $request->openid;
            if(empty($openid)){
                return redirect('run/login');
            }
            $user = MerchantUser::where(['wx_openid' => $openid])->first();
            if(!$user){
                return redirect('/run/login');
            }
            \Auth::guard('merchant')->login($user);
        }

        $total_cash = 0;
        $last_month_total_cash = 0;
        $withdraw_cach = 0;
        $hotel_id = 143;

        $page     = $request->get('page',1);
        $pagesize = $request->get('pagesize', 20);
        $where[]  = ['hotel_id', '=', $hotel_id];
        $where[]  = ['status', '=', 2];
        $where[]  = ['is_confirm', '=', 0];

        if (!empty($request->start_time)) {
            $where[] = ['created_at', '>=', $request->start_time];
        }
        if (!empty($request->end_time)) {
            $where[] = ['created_at', '<=', $request->end_time];
        }
        $hotel = Hotel::where(['id'=> $hotel_id])->first();
        $new_order_list = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code', 'is_confirm', 'confirm_time', 'voice',
                'room_type', 'seller_name', 'seller_address'
            )
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return view('merchant.mobile.run.home',compact('hotel','total_cash','last_month_total_cash','withdraw_cach','new_order_list'));
    }

    // 订单列表
    public function orderList(Request $request){

        return view('merchant.mobile.run.orderLists');
    }

    // 订单详情
    public function orderDetail(Request $request,$order_no){
        $info = BookingOrder::where(['out_trade_no'=> $order_no])->select(
            'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
            'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
            'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
            'code', 'is_confirm', 'confirm_time', 'voice',
            'room_type', 'seller_name', 'seller_address'
        )->first();
        return view('merchant.mobile.run.orderDetail',compact('info'));
    }

    // 操作保存
    public function actionSave(Request $request){
        if (!\Auth::guard('run')->check()) {
            return returnData(403,0,[],'未登陆，无权操作');
        }
        $request->validate(
            [
                'order_no' => 'required',
                'confirm_type' => 'required',
            ], [
                'order_no.required' => '订单编号 不能为空',
                'confirm_type.required' => '确认类型 不能为空',
            ]
        );
        $orderinfo = BookingOrder::where(['out_trade_no'=> $request->order_no,'hotel_id'=> Admin::user()->hotel_id])->first();
        if(!$orderinfo){
            return returnData(404,0,[],'找不到订单信息');
        }
        BookingOrder::orderConfirm($request->order_no);

        return returnData(200,1,[],'ok');
    }
}
