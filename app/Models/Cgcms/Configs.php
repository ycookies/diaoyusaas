<?php

namespace App\Models\Cgcms;


use Illuminate\Database\Eloquent\Model;

class Configs extends BaseModel
{
	
    protected $table = 'config';
    public $timestamps = false;
    public $guarded = [];

    /**
     * @desc 保存参数
     * @param $insdata 参数键值对数组
     * @param string $group_name 分组名
     * @return bool
     * author eRic
     * dateTime 2024-03-23 10:55
     */
    public static function createRow($insdata,$group_name = 'default') {
        foreach ($insdata as $key => $values) {
            if(empty($values)){
                continue;
            }
            $info = self::where(['name' => $key])->count();
            if (!empty($info)) {
                self::where(['name' => $key])->update(['value' => $values]);
            } else {
                $rowdata = [
                    'inc_type' => $group_name,
                    'name'   => $key,
                    'value' => $values,
                ];
                self::create($rowdata);
            }

        }

    }

    // 删除参数
    public static function delsRow(array $field_arr,$group_name = 'default') {
        self::whereIn('name', $field_arr)->delete();
        return true;
    }

    /**
     * @desc 获取参数列表值
     * @param array $field_keys 参数键数组
     * @return array
     * author eRic
     * dateTime 2024-03-23 10:55
     */
    public static function getlists(array $field_keys,$group_name = ''){
        $data = [];
        if(!empty($field_keys)){
            //$field_arr = explode('.',$field_keys);
            $list = self::whereIn('name',$field_keys)->get();
        }
        if(!empty($group_name)){
            $list = self::where(['inc_type'=> $group_name])->get();
        }
        foreach ($list as $key => $items) {
            if(json_decode($items['value'],true) !== null ){
                $data[$items['name']] = json_decode($items['value'],true);
            }else{
                $data[$items['name']] = $items['value'];
            }

        }
        return $data;
    }

}
