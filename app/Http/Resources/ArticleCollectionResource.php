<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Orion\Http\Resources\Resource;
use Orion\Http\Resources\CollectionResource;
use Illuminate\Http\Resources\Json\ResourceCollection ;

class ArticleCollectionResource extends CollectionResource {



    /**
     * 合并一些数据到响应
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        /*return $this->filterFields([
            'id'         => $this->id,d
            'type_id'    => $this->type_id,
            //'title' => $this->client->first_name,
            'title'      => $this->title,
            'contents'   => $this->contents,
            "created_at" => Carbon::parse($this->created_at)->toDateTimeString(),
            "updated_at" => Carbon::parse($this->updated_at)->toDateTimeString(),
        ]);*/
        return $this->toArrayWithMerge($request, [
            'field' => 'new val'
        ]);
        //return parent::toArray($request);
    }

    /**
     * 返回应该和资源一起返回的其他数据数组。
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request) {
        return [
            'code'   => '',// $this->response()->getCharset(),
            'msg'    => '',
            'status' => ''
        ];
    }

    /**
     * 自定义资源响应
     *
     * @param  \Illuminate\Http\Request
     * @param  \Illuminate\Http\Response
     * @return void
     */
    public function withResponse($request, $response) {
        $response->header('X-Value', 'True');
        //$response->content();
    }
}
