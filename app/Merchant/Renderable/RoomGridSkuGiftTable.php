<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use App\Models\Hotel\RoomSkuGift;
use Dcat\Admin\Admin;

class RoomGridSkuGiftTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new RoomSkuGift(), function (Grid $grid) {
            if(!empty($this->payload['gift_id'])){
                $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->whereIn('id',$this->payload['gift_id'])->orderBy('id', 'DESC');
            }else{
                $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->whereIn('id',[0])->orderBy('id', 'DESC');
            }
            $grid->column('sku_gift_name','礼包名');
            //$grid->column('sku_gift_brief','礼包简介');
            $grid->column('sku_gift_desc','礼包描述')->limit(50);
            $grid->column('sku_gift_price','礼包销价');
            $grid->disableRowSelector();
            $grid->disableRefreshButton();
            $grid->disableFilterButton();
            $grid->disableCreateButton();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
            $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name')->width(4);
            });
        });
    }
}
