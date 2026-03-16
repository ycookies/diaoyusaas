<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\Article;
use Dcat\Admin\Admin;
use App\Models\Hotel\Huodong;
use App\Models\Hotel\HuodongUser;

class HuodongBaomingTable extends LazyRenderable
{
    public function grid(): Grid
    {
        info($this->payload);
        return Grid::make(HuodongUser::with('user'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('id');
            $grid->column('user.avatar','报名用户')->image('','44','44');
            $grid->column('user.nick_name','报名用户');
            $grid->column('bm_name','参加人');
            $grid->column('bm_phone','参加人电话');
            $grid->column('created_at','报名时间');
            //$grid->quickSearch(['user_id']);
            $grid->paginate(10);
            $grid->disableActions();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('user_id')->width(4);
            });
        });
    }
}
