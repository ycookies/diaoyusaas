<?php

namespace App\Merchant\Controllers\Tuangou\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Help;
use Dcat\Admin\Admin;

class HelpsTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(Help::with('type'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('id','id');
            $grid->column('type.name','所属分类');
            $grid->column('title','标题');
            //$grid->tools('123');
            //$grid->column('main_img')->image('','50');
            //$grid->disableRowSelector();
            $grid->disableRefreshButton();
            //$grid->disableFilterButton();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->paginate(5);
            $grid->quickSearch(['title'])->placeholder('标题');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                //$filter->like('goods_name')->width(4);
            });
        });
    }
}
