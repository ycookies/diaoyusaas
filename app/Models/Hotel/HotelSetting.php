<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Model;

class HotelSetting extends HotelBaseModel
{
	
    protected $table = 'hotel_setting';
    protected $guarded = [];

    /**
     * @desc 保存参数
     * @param $insdata 参数键值对数组
     * @param $hotel_id 商户ID
     * @param string $group_name 分组名
     * @return bool
     * author eRic
     * dateTime 2024-03-23 10:55
     */
    public static function createRow($insdata,$hotel_id,$group_name = 'default') {
        if(empty($hotel_id)){
           return false;
        }
        foreach ($insdata as $key => $values) {
            if($values == ''){
                continue;
            }
            $info = self::where(['hotel_id' => $hotel_id, 'field_key' => $key])->count();
            if (!empty($info)) {
                self::where(['hotel_id' => $hotel_id, 'field_key' => $key])->update(['field_value' => $values,'group_name' => $group_name]);
            } else {
                $rowdata = [
                    'group_name' => $group_name,
                    'hotel_id'     => $hotel_id,
                    'field_key'   => $key,
                    'field_value' => $values,
                ];
                self::create($rowdata);
            }

        }

    }

    public static function delsRow(array $field_arr,$hotel_id,$group_name = 'default') {
        if(empty($hotel_id)){
            return false;
        }

        self::where(['hotel_id' => $hotel_id])->whereIn('field_key', $field_arr)->delete();
        return true;
    }

    /**
     * @desc 获取参数列表值
     * @param array $field_keys 参数键数组
     * @param $hotel_id 商户ID
     * @return array
     * author eRic
     * dateTime 2024-03-23 10:55
     */
    public static function getlists(array $field_keys,$hotel_id,$group_name = ''){
        $data = [];
        if(!empty($group_name)){
            $list = HotelSetting::where('group_name',$group_name)->where(['hotel_id'=>$hotel_id])->get();
        }else{
            $list = HotelSetting::whereIn('field_key',$field_keys)->where(['hotel_id'=>$hotel_id])->get();
        }
        foreach ($list as $key => $items) {
            $data[$items['field_key']] = $items['field_value'];
        }
        return $data;
    }
    
}
