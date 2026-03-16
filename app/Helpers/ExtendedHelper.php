<?php
/**
 * 生成编码
 */
if (!function_exists('generateNu')) {
    function generateNu($type=1,$community_id)
    {
        $community = \App\Models\Community::find($community_id);

        $code = $community->code;
        $date = date('ymd');

        $key = "{$code}-{$type}-{$date}";

        $nu = \Illuminate\Support\Facades\Cache::get($key);
        if(empty($nu)){
            \Illuminate\Support\Facades\Cache::add($key, 1, 60*30*60);
        }else{
            \Illuminate\Support\Facades\Cache::put($key, intval($nu)+1, 60*30*60);
        }
        return $code . $type . $date . sprintf("%05d", intval($nu)+1);
    }
}
