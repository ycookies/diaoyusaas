<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use App\Models\Hotel\MiniprogramPage;
use Dcat\Admin\Admin;
class MinappPagesTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new MiniprogramPage(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id');
            //$grid->column('id','ID');
            $grid->column('type','所属分类')->using(MiniprogramPage::Type_arr);
            //$grid->column('click');
            $grid->column('name','标题');
            $grid->column('path','页面路径');
            $grid->quickSearch(['name','type']);
            $grid->disableActions();
            $grid->quickSearch(['name', 'path'])->placeholder('页面名称,路径');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name')->width(4);
            });
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('type', '所属分类', MiniprogramPage::Type_arr);
            });
            $grid->paginate(6);
        });
    }
}
