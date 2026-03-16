<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use App\Models\Hotel\RoomSkuTag;
use Dcat\Admin\Admin;

class RoomSkuTagsTable extends LazyRenderable
{
    public $room_id;
    public function grid(): Grid
    {
        return Grid::make(new RoomSkuTag(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->whereIn('id',$this->payload['tags_id'])->orderBy('id', 'DESC');
            $grid->column('sku_tags_name','名称');
            $grid->column('sku_tags_tips','描述');
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
