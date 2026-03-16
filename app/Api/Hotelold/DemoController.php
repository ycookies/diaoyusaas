<?php

namespace App\Api\Hotelold;

//use Illuminate\Http\Request;
use App\Models\Article;
use Orion\Concerns\DisablePagination;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Requests\Request;
class DemoController extends BaseController
{

    /**
     * |        | GET|HEAD  | api/posts                                       | api.posts.index                        | App\Http\Controllers\Api\PostsController@index                            | api                                             |
    |        | POST      | api/posts/search                                | api.posts.search                       | App\Http\Controllers\Api\PostsController@index                            | api                                             |
    |        | POST      | api/posts                                       | api.posts.store                        | App\Http\Controllers\Api\PostsController@store                            | api                                             |
    |        | GET|HEAD  | api/posts/{post}                                | api.posts.show                         | App\Http\Controllers\Api\PostsController@show                             | api                                             |
    |        | PUT|PATCH | api/posts/{post}                                | api.posts.update                       | App\Http\Controllers\Api\PostsController@update                           | api                                             |
    |        | DELETE    | api/posts/{post}                                | api.posts.destroy                      | App\Http\Controllers\Api\PostsController@destroy                          | api                                             |
    |        | POST      | api/posts/batch                                 | api.posts.batchStore                   | App\Http\Controllers\Api\PostsController@batchStore                       | api                                             |
    |        | PATCH     | api/posts/batch                                 | api.posts.batchUpdate                  | App\Http\Controllers\Api\PostsController@batchUpdate                      | api                                             |
    |        | DELETE    | api/posts/batch                                 | api.posts.batchDestroy
     */
    // 取消授权访问
    use DisableAuthorization;

    // 指定数据模型
    protected $model = Article::class;


    // 数据验证规则
    protected $request = \App\Http\Requests\ArticleRequest::class;

    // 响应数据
    protected $resource  = \App\Http\Resources\ArticleResource::class;

    // 响应数据合集
    protected $collectionResource = \App\Http\Resources\ArticleCollectionResource::class;


    // 列表
    public function index(Request $Request){
        return parent::index($Request);
    }
    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    protected function includes() : array
    {
        return ['user', 'meta'];
    }

    /**
     *
     * 设置表key
     *
     * @return string
     */
    protected function keyName(): string
    {
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
