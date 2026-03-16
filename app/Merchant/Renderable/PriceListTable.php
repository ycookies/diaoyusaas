<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomSkuTag;
use Dcat\Admin\Admin;

class PriceListTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new RoomSkuPrice(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id,'room_id'=> $this->payload['room_id']])->orderBy('id', 'DESC');
            $grid->column('roomsku_title','标题');
            $grid->column('roomsku_where_str','条件')->explode(',')->badge();
            $grid->column('roomsku_gift_str','礼包')->badge();
            $grid->column('roomsku_tags_str','标签')->badge();
            $grid->column('roomsku_give_points','赚送积分');
            $grid->column('roomsku_give_coupon','赚送优惠券');
            $grid->column('roomsku_price','销售价');
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
