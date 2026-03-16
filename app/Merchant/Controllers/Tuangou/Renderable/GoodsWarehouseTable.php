<?php

namespace App\Merchant\Controllers\Tuangou\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Goods\GoodsWarehouse;
use Dcat\Admin\Admin;

class GoodsWarehouseTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(GoodsWarehouse::with('cats'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('id','商品ID');
            $grid->column('cats.name','分类')->label();
            $grid->column('main_img','主图')->image('','100');
            $grid->column('goods_name','商品名称');
            $grid->column('original_price','原价');
            $grid->column('cost_price','成本价');
            //$grid->column('main_img')->image('','50');
            //$grid->disableRowSelector();
            $grid->disableRefreshButton();
            $grid->disableFilterButton();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->paginate(5);
            $grid->quickSearch(['goods_name'])->placeholder('商品名称');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                //$filter->like('goods_name')->width(4);
            });
        });
    }
}
