<?php

namespace App\Api\Hotelold;

use App\Models\Hotel\UserMember;
use App\Models\Hotel\WxappConfig;
use App\Services\WxUserService;
use EasyWeChat\Factory;
use Illuminate\Http\Request as apiRequest;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Requests\Request;

class UserController extends BaseController {
    // 取消授权访问
    use DisableAuthorization;

    // 指定数据模型
    protected $model = UserMember::class;


    // 数据验证规则
    protected $request = \App\Http\Requests\UserRequest::class;

    // 响应数据
    protected $resource = \App\Http\Resources\UserResource::class;

    // 响应数据合集
    //protected $collectionResource = \App\Http\Resources\ArticleCollectionResource::class;

    /**
     * 可用查询范围的列表。
     *
     * @return array
     */
    protected function exposedScopes(): array {
        return ['id', 'name'];
    }

    /**
     * 可以筛选的字段
     *
     * @return array
     */
    protected function filterableBy(): array {
        return ['id', 'name', 'created_at'];
    }

    /**
     * 可以搜索的属性
     *
     * @return array
     */
    protected function searchableBy(): array {
        return ['name'];
    }

    /**
     * 可以排序的字段
     *
     * @return array
     */
    protected function sortableBy(): array {
        return ['id', 'name'];
    }

    // 列表
    public function index(Request $request) {
        $user = Auth::guard('api')->user();
        return parent::index($request);
    }

    // 查询 需要使用json
    // {"api_token":"3Gk6S5oWuzcI67BsDCcSXPs3qP9bQ0v0slWF8JLEkg9Hd2n5Uua4JSQpa4St3zaF","sort":[{"field" : "id", "direction" : "desc"}]}
    /*public function search(Request $Request){
        return parent::search($Request);
    }*/

    // 详情
    public function show(Request $request, $id) {
        $user = Auth::guard('api')->user();
        return parent::show($request, $id);
    }

    // 新增数据
    public function store(Request $request) {
        return parent::store($request);
    }

    // 删除数据
    public function destroy(Request $request, $id) {
        return parent::destroy($request, $id);
    }
    //
    public function wxMinRegister(apiRequest $request){
        $data      = $request->all();
        $code      = $request->get('code'); // 授权码
        $avatarUrl = $request->get('avatarUrl', 'https://mmbiz.qpic.cn/mmbiz/icTdbqWNOwNRna42FI242Lcia07jQodd2FJGIYQfG0LAJGFxM4FbnQP6yfMxBgJ0F3YRqJCJ1aPAK2dQagdusBZg/0');
        $user_nick = $request->get('user_nick', '微信用户'); //微信昵称
        $mobile    = $request->get('mobile', '0'); // 手机
        $mall_id   = $request->get('mall_id', 1); // 商城
        $parent_id = $request->get('parent_id', 0); // 上级
        $gender    = $request->get('gender', ''); // 上级
        $password = $request->get('password', '12345678'); // 密码
        $wxconfig = WxappConfig::where(['mall_id' => $mall_id])->first();
        $config   = [
            'app_id'  => $wxconfig->appid,
            'secret'  => $wxconfig->appsecret,
            'token'   => '',
            'aes_key' => '',
        ];

        $app  = Factory::miniProgram($config);
        $info = $app->auth->session($code);

        // 微信登陆失败
        if (!empty($info['errcode'])) {
            return returnData(50001);
        }
        // 新增用户
        $info      = (new WxUserService)->addUser([
            'mall_id'      => $mall_id,
            'mch_id'       => 0,
            'username'     => $user_nick,
            'nickname'     => $user_nick,
            'openid'       => $info['openid'],
            'avatarUrl'    => $avatarUrl,
            'mobile'       => $mobile,
            'parent_id'    => $parent_id,
            'gender'       => $gender,
            'password' => $password,
        ]);
        //$userinfo ='';
        $reqdata = [
            'user_id'   => $info['user_id'],
            'api_token' => $info['api_token'],
            'avatarUrl' => $avatarUrl,
            'user_nick' => $user_nick,
        ];
        return returnData(200, 1, $reqdata);
    }

    // 小程序用户登陆
    public function wxMinLogin(apiRequest $request) {
        $data      = $request->all();
        $code      = $request->get('code'); // 授权码
        $avatarUrl = $request->get('avatarUrl', 'https://mmbiz.qpic.cn/mmbiz/icTdbqWNOwNRna42FI242Lcia07jQodd2FJGIYQfG0LAJGFxM4FbnQP6yfMxBgJ0F3YRqJCJ1aPAK2dQagdusBZg/0');
        $user_nick = $request->get('user_nick', '微信用户'); //微信昵称
        $mobile    = $request->get('mobile', '0'); // 手机
        $mall_id   = $request->get('mall_id', 1); // 商城
        $parent_id = $request->get('parent_id', 0); // 上级
        $gender    = $request->get('gender', ''); // 上级
        $password = $request->get('password', '12345678'); // 密码
        $wxconfig = WxappConfig::where(['mall_id' => $mall_id])->first();
        $config   = [
            'app_id'  => $wxconfig->appid,
            'secret'  => $wxconfig->appsecret,
            'token'   => '',
            'aes_key' => '',
        ];

        $app  = Factory::miniProgram($config);
        $info = $app->auth->session($code);

        // 微信登陆失败
        if (!empty($info['errcode'])) {
            return returnData(50001);
        }
        // 新增用户
        $info      = (new WxUserService)->addUser([
            'mall_id'      => $mall_id,
            'mch_id'       => 0,
            'username'     => $user_nick,
            'nickname'     => $user_nick,
            'openid'       => $info['openid'],
            'avatarUrl'    => $avatarUrl,
            'mobile'       => $mobile,
            'parent_id'    => $parent_id,
            'gender'       => $gender,
            'password' => $password,
        ]);
        //$userinfo ='';
        $reqdata = [
            'user_id'   => $info['user_id'],
            'api_token' => $info['api_token'],
            'avatarUrl' => $avatarUrl,
            'user_nick' => $user_nick,
        ];
        return returnData(200, 1, $reqdata);
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    protected function includes(): array {
        return ['user', 'meta'];
    }

    /**
     *
     * 设置表key
     *
     * @return string
     */
    protected function keyName(): string {
        return 'id';
    }
    /**
     * 基于看守器取回当前通过验证的用户。.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    /*public function resolveUser()
    {
        return Auth::guard('api')->user();
    }*/
}
