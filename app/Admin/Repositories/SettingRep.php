<?php

namespace App\Admin\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Form;

class SettingRep extends Repository
{
    // 返回你的id字段名称，默认“id”
    protected $keyName = '_id';
    // 查询编辑页数据
    // 这个方法需要返回一个数组
    public function edit(Form $form)
    {
        $data = [];
        return $data;
    }

    // 这个方法用于在修改数据前查询原记录
    // 如果使用了文件上传表单，当文件发生变更时会根据这个原始记录自动删除旧文件
    // 如果不需要此数据返回空数组即可
    public function updating(Form $form)
    {
        // 获取id
        $id = $form->builder()->getResourceId();

        return [];
    }

    // 修改操作
    // 返回一个bool类型的数据
    public function update(Form $form)
    {
        // 获取id
        $id = $form->builder()->getResourceId();

        // 获取要修改的数据
        $attributes = $form->updates();

        // TODO
        // 这里写你的修改逻辑

        return true;
    }

}
