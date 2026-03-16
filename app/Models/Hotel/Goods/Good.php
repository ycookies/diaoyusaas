<?php

namespace App\Models\Hotel\Goods;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;

class Good extends HotelBaseModel
{
    use SoftDeletes;
    const Status_up = 1;
    const Status_dm = 0;
    protected $table = 'goods';
    protected $guarded = [];

    public function getPriceAttribute($value){
        return formatFloat($value);
    }



    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Hotel\Goods\GoodsWarehouse::class, 'goods_warehouse_id')->withTrashed();
    }

    public function purchaseNotices(){
        return $this->belongsTo(\App\Models\Hotel\Help::class, 'purchase_notices_art_id');
    }

    public function serviceGuarantees(){
        return $this->belongsTo(\App\Models\Hotel\Help::class, 'service_guarantees_art_id');
    }

    // 商品上架
    public static function goodsUp($goods_id){
        $status = Good::where(['id' => $goods_id])->update(['status'=> 1]);
        return $status;
    }

    // 商品下架
    public static function goodsDm($goods_id){
        $status = Good::where(['id' => $goods_id])->update(['status'=> 0]);
        return $status;
    }

    // 商品库因恢复软删除上架
    public static function warehouseToGoodsUp($goods_warehouse_id){
        $status = Good::where(['goods_warehouse_id' => $goods_warehouse_id])->update(['status'=> 1]);
        return $status;
    }

    // 商品库因恢复软删除下架
    public static function warehouseTogoodsDm($goods_warehouse_id){
        $status = Good::where(['goods_warehouse_id' => $goods_warehouse_id])->update(['status'=> 0]);
        return $status;
    }

    /**
     * @desc 更新小程序码
     * @param $id
     * @param $imgurl
     * author eRic
     * dateTime 2025-03-17 21:42
     */
    public static function upQrcode($id,$imgurl){
        return self::where(['id'=> $id])->update(['goods_share_qrcode'=>$imgurl]);
    }
}
