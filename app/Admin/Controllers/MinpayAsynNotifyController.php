<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\MinpayAsynNotify;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Modal;

// 列表
class MinpayAsynNotifyController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('日志列表')
            ->description('全部')
            ->breadcrumb(['text'=>'日志列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(MinpayAsynNotify::with('hotel'), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('hotel.name','酒店');
            $grid->column('order_no','交易单号');
            /*$grid->column('send_data');
            $grid->column('resp_data');*/
            $grid->column('status','转发状态')->bool();
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                if(empty($actions->row->status)){
                    $form = Form::make(new MinpayAsynNotify());
                    //$form->confirm('确认要重发吗？');
                    $form->action('minpay-asyn-notify/resetAsynNotify');
                    $form->html('重发交易日志到融宝支付系统');
                    //$form->text('username', '账号')->value($actions->row->username)->disable()->required();
                    $form->hidden('id')->value($actions->row->id)->required();
                    $form->disableEditingCheck();
                    $form->disableCreatingCheck();
                    $form->disableViewCheck();
                    $modal = Modal::make()
                        ->lg()
                        ->title('重发交易日志')
                        ->body($form)
                        ->button('<i class="feather icon-arrow-up-right tips" data-title="重发交易日志"></i>');
                    $actions->append($modal);
                }

            });
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
        return Show::make($id, new MinpayAsynNotify(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('order_no');
            $show->field('send_data');
            $show->field('resp_data');
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
        return Form::make(new MinpayAsynNotify(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('order_no');
            $form->text('send_data');
            $form->text('resp_data');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
