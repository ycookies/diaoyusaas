<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Article;
use Orion\Concerns\DisablePagination;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
class ArticleController extends BaseController
{

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
