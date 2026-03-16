<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Orion\Http\Requests\Request;

class RoomRequest extends Request
{
    /**
     * 确定用户是否有权提出此请求
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }


    /**
     * @desc 共公验证规则
     * @return array
     * author eRic
     * dateTime 2022-12-29 20:02
     */
    public function commonRules() : array
    {
        return [
            'seller_id' => 'required|int'
        ];
    }

    /**
     * @desc 新增数据验证规则
     * @return array in:draft,review
     * author eRic
     * dateTime 2022-12-29 20:03
     */
    public function storeRules() : array
    {
        return [
            'name' => 'required',
            //'contents' => 'required',
        ];
    }

    /**
     * @desc 更新数据 验证规则
     * @return array
     * author eRic
     * dateTime 2022-12-29 20:04
     */
    public function updateRules() : array
    {
        return [
            'name' => 'required',
            //'contents' => 'required',
        ];
    }

    public function dfRules(){

    }

    public function messages() : array
    {
        return [
            'seller_id.required'  => '酒店ID不能为空',
        ];
    }
}
