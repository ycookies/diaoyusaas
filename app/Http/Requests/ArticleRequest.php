<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Orion\Http\Requests\Request;

class ArticleRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'title' => 'required|int'
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
            'title' => 'required',
            'contents' => 'required',
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
            'title' => 'required',
            'contents' => 'required',
        ];
    }

    public function dfRules(){

    }

    public function messages() : array
    {
        return [
            'title.required'  => '标题不能为空',
            'contents.required' => '内容不能为空',
        ];
    }
}
