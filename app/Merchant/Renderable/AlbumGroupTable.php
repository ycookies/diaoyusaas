<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\AlbumGroup;
use Dcat\Admin\Admin;

class AlbumGroupTable extends LazyRenderable
{
    public $room_id;
    public function grid(): Grid
    {
        return Grid::make(new AlbumGroup(), function (Grid $grid) {
            $grid->setResource('/album-group');
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','ASC');
            $grid->number();
            $grid->column('name');
            $grid->column('description');
            $grid->column('sort');
            $grid->column('status','启用')->switch();
            $grid->tools();
            $grid->enableDialogCreate();
            $title = '<a  href="javascript:void(0);" data-url="'.admin_url('album-group/create').'" class="btn btn-success btn-sm add-album-group">添加分组</a>';
            \Dcat\Admin\Form::dialog('添加分组')
                ->click('.add-album-group')
                ->success('Dcat.reload()')
                ->dimensions('600px', '400px');
            $grid->showQuickEditButton();
            $grid->tools($title);
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                $actions->append(new \App\Merchant\Actions\Grid\DelAlbumGroupAction);
                // 去掉编辑
                $actions->disableView();
                $actions->disableEdit();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('name')->width(3);

            });
        });
    }
}
