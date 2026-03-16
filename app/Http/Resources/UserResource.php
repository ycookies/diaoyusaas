<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Orion\Http\Resources\Resource;
use Orion\Http\Resources\CollectionResource;
//use Illuminate\Http\Resources\Json\ResourceCollection as ;

class UserResource extends Resource {

    // 要隐藏的字段
    protected $withoutFields = [''];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return $this->filterFields([
            'id'         => $this->id,
            'name'    => $this->name,
            //'title' => $this->client->first_name,
            'phone'      => $this->phone,
            'id_card'   => $this->id_card,
            "created_at" => Carbon::parse($this->created_at)->toDateTimeString(),
            "updated_at" => Carbon::parse($this->updated_at)->toDateTimeString(),
        ]);

        //return parent::toArray($request);
    }

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields) {
        $this->withoutFields = $fields;
        return $this;
    }

    /**
     * 删除过滤元素
     *
     * @param $array
     * @return array
     */
    protected function filterFields($array) {
        return collect($array)->forget($this->withoutFields)->toArray();
    }

}
