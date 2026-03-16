<?php

namespace App\Models\Hotel\Goods;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;
use Illuminate\Support\Facades\Log;
class GoodsWarehouse extends HotelBaseModel
{
    use SoftDeletes;
    protected $table = 'goods_warehouse';
    protected $guarded = []; // 批量赋值的黑名单
    // protected $fillable = []; // 可以作为批量赋值的白名单
    // protected $appends = []; // 追加属性
    // protected $hidden = []; // 数组中的属性会被隐藏

    public function getOriginalPriceAttribute($value){
        return formatFloat($value);
    }
    public function getCostPriceAttribute($value){
        return formatFloat($value);
    }

    public function cats(){
        return $this->belongsTo(\App\Models\Hotel\Goods\GoodsCat::class, 'cats_id');
    }

    public function setPicUrlAttribute($value){
        if (is_array($value)) {
            $this->attributes['pic_url'] = json_encode(array_filter($value), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 注册模型事件
     */
    protected static function boot()
    {
        parent::boot();

        /*// 监听 retrieved 事件（从数据库检索到模型时触发）
        static::retrieved(function ($model) {
            Log::info("User retrieved: {$model->id}");
        });

        // 监听 creating 事件（模型创建之前触发）
        static::creating(function ($model) {
            Log::info("User creating: {$model->id}");
        });

        // 监听 created 事件（模型创建之后触发）
        static::created(function ($model) {
            Log::info("User created: {$model->id}");
        });

        // 监听 updating 事件（模型更新之前触发）
        static::updating(function ($model) {
            Log::info("User updating: {$model->id}");
        });

        // 监听 updated 事件（模型更新之后触发）
        static::updated(function ($model) {
            Log::info("User updated: {$model->id}");
        });

        // 监听 saving 事件（模型保存之前触发，包括创建和更新）
        static::saving(function ($model) {
            Log::info("User saving: {$model->id}");
        });

        // 监听 saved 事件（模型保存之后触发，包括创建和更新）
        static::saved(function ($model) {
            Log::info("User saved: {$model->id}");
        });

        // 监听 deleting 事件（模型删除之前触发）
        static::deleting(function ($model) {
            if ($model->isForceDeleting()) { // 如果是强制删除（非软删除）
                Log::info("User force deleting: {$model->id}");
            } else {
                Log::info("User soft deleting: {$model->id}");
            }
        });*/

        // 监听 deleted 事件（模型删除之后触发）
        static::deleted(function ($model) {
            if ($model->isForceDeleting()) { // 如果是强制删除（非软删除）
                //Log::info("User force deleted: {$model->id}");
            } else {
                \App\Models\Hotel\Goods\Good::warehouseTogoodsDm($model->id);
            }
        });

       /* // 监听 restoring 事件（模型恢复之前触发）
        static::restoring(function ($model) {
            Log::info("User restoring: {$model->id}");
        });

        // 监听 restored 事件（模型恢复之后触发）
        static::restored(function ($model) {
            Log::info("User restored: {$model->id}");
        });*/
    }

}
