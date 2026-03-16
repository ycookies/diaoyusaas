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

use App\Models\Hotel\Assess;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Room;
// 列表
class AssessController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('住客评价')
            ->description('全部')
            ->breadcrumb(['text'=>'住客评价','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Assess::with('user','room'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            //$grid->column('hotel_id');
            $grid->view('admin.access.order-grid-view');
            $grid->column('user.nick_name','用户')->display(function (){
                return "<a href='".admin_url('user-member?_search_='.$this->user_id)."'>".$this->user->nick_name." </a>";
            });
            $grid->column('score');
            $grid->column('content');//->width('200')->limit(20);
            $grid->column('img')->image('','100','60');
            $grid->column('order_sn','订单信息');
            /*$grid->column('time');
            //$grid->column('uniacid');
            $grid->column('reply');
            $grid->column('status');
            $grid->column('reply_time');
            $grid->column('order_sn');*/
            $grid->column('recommend','推荐上首页')->switch();
            $grid->disableBatchDelete();
            // $grid->disableCreateButton();
            $grid->enableDialogCreate();
            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                //
                $actions->disableView();
            });
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id','用户ID')->width(3);
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
        return Show::make($id, new Assess(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('score');
            $show->field('content');
            $show->field('img');
            $show->field('time');
            $show->field('user_id');
            $show->field('uniacid');
            $show->field('reply');
            $show->field('status');
            $show->field('reply_time');
            $show->field('order_sn');
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
        return Form::make(new Assess(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->select('user_id')->options(\App\User::where(['hotel_id'=> Admin::user()->hotel_id])->pluck('name','id'))->required();

            $form->select('score')->options(Assess::Star_arr)->required();
            $form->text('ruzhu_date','入驻日期');
            $form->select('room_type','房型')->options(Room::where(['hotel_id'=> Admin::user()->hotel_id])->pluck('name','id'))->required();
            $form->hidden('recommend');
            $form->textarea('content')->required();
            $form->multipleImage('img')
                ->saveFullUrl()->url('/upload/imgs')
                ->autoUpload()
                ->removable(false)
                ->autoSave(false)->saveAsJson();
            //$form->text('uniacid');
            //$form->text('reply');
            //$form->text('status');
            //$form->text('reply_time');
            //$form->text('order_sn');

            $form->display('created_at');
            $form->display('updated_at');
            // 保存前
            $form->submitted(function (Form $form) {
                // 修改操作
                if($form->isEditing()){
                    // 获取原始数据
                    $original_data =  $form->model()->getOriginal();

                }
            });

        });
    }
}
