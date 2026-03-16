<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use Dcat\Admin\Admin;
class ArtTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(Article::with('type'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id');
            $grid->column('title_as','文章ID');
            $grid->column('type.name','所属分类');
            //$grid->column('click');
            $grid->column('title','标题');
            $grid->quickSearch(['title']);
            $grid->paginate(10);
            $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('title')->width(4);
            });
        });
    }
}
