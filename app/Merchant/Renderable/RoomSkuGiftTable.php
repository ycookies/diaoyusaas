<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use App\Models\Hotel\RoomSkuGift;
use Dcat\Admin\Admin;

class RoomSkuGiftTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new RoomSkuGift(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('sku_gift_name','礼包名');
            $grid->column('sku_gift_brief','礼包简介');
            //$grid->column('sku_gift_desc','礼包描述');
            $grid->column('sku_gift_price','礼包销价');
            //$grid->disableRowSelector();
            //$grid->disableRefreshButton();
            $grid->disableFilterButton();
            //$grid->disableCreateButton();
            $grid->tools("<a href='".admin_url('/room-sku-gift/create')."' class='btn btn-primary' target='_blank'>添加一项礼包</a>");
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
