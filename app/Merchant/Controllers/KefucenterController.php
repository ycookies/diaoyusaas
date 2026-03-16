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

use App\Models\Hotel\Kefucenter;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Models\Hotel\Hotel;
use Dcat\Admin\Widgets\Modal;
use App\Merchant\Renderable\KefuUI;
// 列表
class KefucenterController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        Admin::js([
            '/js/amr/libamr-min.js',
            '/js/amr/amr-player.js'
        ]);
        return $content
            ->header('客服消息管理')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->row($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Kefucenter::with('hotel'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('msg_info','平台/用户')->display(function (){
                $user_name = $this->user_openid;
                $info  = $this->platform_str.'<br/><span class="text-muted">'.$user_name.'</span>';
                if(!empty($this->user_openid)){
                    $user_info = \App\User::where(['openid'=> $this->user_openid])->first();
                    if(!empty($user_info->nick_name)){
                        $user_name = $user_info->nick_name;
                        $info  = $this->platform_str.'<br/><span class="text-muted"> <a href="'.url('merchant/user-member?id='.$user_info->id).'">'.$user_name.'</a></span>';
                    }
                }

                return $info;
            });
            //$grid->column('hotel.name');
            //$grid->column('app_id');
            //$grid->column('platform');
            //$grid->column('user_id');
            //$grid->column('user_openid');
            $grid->column('msg_to','作者')->using(Kefucenter::Msg_to)->label([
                1 => 'info',
                2 => 'success',
            ]);
            //$grid->column('msg_type','类型')->using(Kefucenter::$msg_type_arr);
            $grid->column('msg_content','消息')->display(function (){
                if($this->msg_type == 'image'){
                    return "<img class='' src='".$this->msg_content."' width='100'>";
                }
                if($this->msg_type == 'voice'){
                    $msg_content = $this->msg_content;
                    $html = <<<HTML
<div style='min-width: 60px;background:#dddddd;padding: 3px' onclick="playAMRs('$msg_content')"> <i class="voice-play feather icon-volume-1 zi_danger"></i></div>
HTML;

                    return $html;
                }
                return $this->msg_content;
            });
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $modal = Modal::make();
                $modal->title('客服回复消息');
                $modal->body(KefuUI::make()->payload(['user_openid'=> $actions->row->user_openid]));
                $modal->button('回复');
                $actions->append($modal->render());
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('platform','平台')->select(['wxgzh'=> '公众号','minapp'=> '小程序'])->width(3);
                $filter->equal('msg_to','作者')->select(['1'=> '用户','2'=> '酒店'])->width(3);
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
        return Show::make($id, new Kefucenter(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('app_id');
            $show->field('platform');
            $show->field('user_openid');
            $show->field('msg_to');
            $show->field('msg_type');
            $show->field('msg_content');
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
        return Form::make(new Kefucenter(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('app_id');
            $form->text('platform');
            $form->text('user_openid');
            $form->text('msg_to');
            $form->text('msg_type');
            $form->text('msg_content');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
