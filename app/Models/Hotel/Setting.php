<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class Setting extends HotelBaseModel
{
	
    protected $table = 'setting';
    protected $guarded = [];

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
            /*if(empty($values)){
                continue;
            }*/
            $info = self::where(['field_key' => $key])->count();
            if (!empty($info)) {
                self::where(['field_key' => $key])->update(['field_value' => $values]);
            } else {
                $rowdata = [
                    'group_name' => $group_name,
                    'field_key'   => $key,
                    'field_value' => $values,
                ];
                self::create($rowdata);
            }

        }

    }

    // 删除参数
    public static function delsRow(array $field_arr,$group_name = 'default') {
        self::whereIn('field_key', $field_arr)->delete();
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
            $list = self::whereIn('field_key',$field_keys)->get();
        }
        if(!empty($group_name)){
            $list = self::where(['group_name'=> $group_name])->get();
        }
        foreach ($list as $key => $items) {
            if(json_decode($items['field_value'],true) !== null ){
                $data[$items['field_key']] = json_decode($items['field_value'],true);
            }else{
                $data[$items['field_key']] = $items['field_value'];
            }

        }
        return $data;
    }
}
