<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers;

use App\Models\Hotel\Ad;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class AdController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('广告列表')
            ->description('全部')
            ->breadcrumb(['text'=>'广告列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * @copyright ©2023 杨光
     * @author 杨光
     * @link https://www.saishiyun.net/
     * @contact wx:Q3664839
     * Created by Phpstorm
     * 学习永无止镜 践行开源公益
     */

namespace App\Merchant\Controllers;

    use App\Models\Hotel\Ad;
    use Dcat\Admin\Form;
    use Dcat\Admin\Grid;
    use Dcat\Admin\Show;
    use Dcat\Admin\Http\Controllers\AdminController;
    use Dcat\Admin\Layout\Content;
    use Dcat\Admin\Admin;

// 列表
class AdController extends AdminController {

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('广告列表')
            ->description('全部')
            ->breadcrumb(['text' => '广告列表', 'uri' => ''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        return Grid::make(new Ad(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('title');
            $grid->column('logo', '广告图')->image('', '100px');
            $grid->column('state', '链接类型')->using(Ad::State_arr);
            $grid->column('links')->display(function ($e) {
                return $this->src;
            });
            $grid->column('status')->switch();
            /*$grid->column('src');
            $grid->column('orderby');
            $grid->column('xcx_name');
            $grid->column('uniacid');
            $grid->column('type');
            $grid->column('wb_src');

            $grid->column('appid');*/
            $grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉查看
                $actions->disableView();
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
    protected function detail($id) {
        return Show::make($id, new Ad(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('title');
            $show->field('logo');
            $show->field('status');
            $show->field('src');
            $show->field('orderby');
            $show->field('xcx_name');
            $show->field('uniacid');
            $show->field('type');
            $show->field('wb_src');
            $show->field('state');
            $show->field('appid');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        return Form::make(new Ad(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            $form->text('title');
            $form->image('logo', '广告图')->disk('admin')->width(3)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->removable(false)->autoSave(false)->autoUpload()->required();
            $form->radio('type', '广告位置')->options(Ad::Type_arr)->required();
            $form->radio('state', '链接类型')
                ->when('1', function (Form $form) {
                    $form->text('src', '小程序页面地址');
                })
                ->when('2', function (Form $form) {
                    $form->text('wb_src', '外部网页地址');
                })
                ->when('3', function (Form $form) {
                    $form->text('appid', '小程序appid')->help('需要先绑定两个小程序的关联');
                })
                ->options(Ad::State_arr)->required();
            $form->switch('status');
            /*;
            $form->text('orderby');
            $form->text('xcx_name');
            $form->text('uniacid');
            $form->text('wb_src');
            $form->text('state');
            $form->text('appid');*/

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}

/**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Ad(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('title');
            $grid->column('logo','广告图')->image('','100px');
            $grid->column('state','链接类型')->using(Ad::State_arr);
            $grid->column('links')->display(function ($e){
                return $this->src;
            });
            $grid->column('status')->switch();
            /*$grid->column('src');
            $grid->column('orderby');
            $grid->column('xcx_name');
            $grid->column('uniacid');
            $grid->column('type');
            $grid->column('wb_src');

            $grid->column('appid');*/
            $grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉查看
                $actions->disableView();
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
        return Show::make($id, new Ad(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('title');
            $show->field('logo');
            $show->field('status');
            $show->field('src');
            $show->field('orderby');
            $show->field('xcx_name');
            $show->field('uniacid');
            $show->field('type');
            $show->field('wb_src');
            $show->field('state');
            $show->field('appid');
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
        return Form::make(new Ad(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            $form->text('title');
            $form->image('logo','广告图')->disk('admin')->width(3)->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->removable(false)->autoSave(false)->autoUpload()->required();
            $form->radio('type','广告位置')->options(Ad::Type_arr)->required();
            $form->radio('state','链接类型')
                ->when('1', function (Form $form) {
                    $form->text('src','小程序页面地址');
                })
                ->when('2', function (Form $form) {
                    $form->text('wb_src','外部网页地址');
                })
                ->when('3', function (Form $form) {
                    $form->text('appid','小程序appid')->help('需要先绑定两个小程序的关联');
                })
                ->options(Ad::State_arr)->required();
            $form->switch('status');
            /*;
            $form->text('orderby');
            $form->text('xcx_name');
            $form->text('uniacid');
            $form->text('wb_src');
            $form->text('state');
            $form->text('appid');*/

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
