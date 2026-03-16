<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\Album;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Form as WidgetForm;
use App\Models\Hotel\AlbumGroup;

// 列表
class AlbumController extends AdminController
{


    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','url'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $cache_key = Admin::user()->hotel_id.'album_group';
        $album_group = \Cache::get($cache_key);
        if(empty($album_group)){

            \Cache::put($cache_key,$album_group,2560000);
        }
        return Grid::make(Album::with('albumGroup'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->number();
            $grid->enableDialogCreate();
            $grid->column('albumGroup.name','分组');
            $grid->column('title');
            $grid->column('file_path')->image('','50');
            $grid->column('created_at');
            //$grid->setActionClass(Grid\Displayers\Actions::class);
            $tab = \Dcat\Admin\Widgets\Tab::make()->tabStyle( 'nav-pills justify-content-center');
            //$tab->vertical();
            $cache_key = Admin::user()->hotel_id.'album_group';
            //$album_group = \Cache::get($cache_key);
            $album_group = AlbumGroup::where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('sort','ASC')->get()->toArray();
            $request = request();
            $scope = $request->get('_scope_');

            $tab->addLink('全部', admin_url('album'),empty($scope) ? true:false);
            if(!empty($album_group)){
                foreach ($album_group as $items){
                    $tab->addLink($items['name'], admin_url('album').'?_scope_=album_group_id'.$items['id'],$scope == 'album_group_id'.$items['id'] ? true:false);
                }
            }
            $grid->header($tab->render())->headerCenterStyle('600px');

            $modal = \Dcat\Admin\Widgets\Modal::make('管理相册分组')->lg();
            $modal->body(\App\Merchant\Renderable\AlbumGroupTable::make());
            $modal->button('<button class="btn btn-primary btn-mini">管理相册分组</button>');
            $grid->tools($modal->render());
            //$grid->tools('<button class="btn btn-primary btn-mini">管理相册分组</button>');
            //
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                /**
                 * 分组.
                 */
                //$cache_key = Admin::user()->hotel_id.'album_group';
                $album_group = AlbumGroup::where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('sort','ASC')->get()->toArray();
                if(!empty($album_group)){
                    foreach ($album_group as $items){
                        $filter->scope('album_group_id'.$items['id'], $items['name'])->where('album_group_id', $items['id']);
                    }
                }

            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Album(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('album_group_id');
            $show->field('title');
            $show->field('description');
            $show->field('file_path')->image();
            $show->field('file_name');
            $show->field('file_size');
            $show->field('file_type');
            $show->field('width');
            $show->field('height');
            $show->field('sort');
            $show->field('is_cover');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Album(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->radio('album_group_id','选择分类')
                ->options(AlbumGroup::where(['hotel_id'=>Admin::user()->hotel_id])->pluck('name','id'))->required();
            $form->text('title')->required();
            $form->text('description');
            $form->multipleImage('file_path')->disk('hotel_'.Admin::user()->hotel_id)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required()->saveAsJson();
            $form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
