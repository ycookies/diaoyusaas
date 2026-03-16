<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\WxappConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class WxappConfigController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WxappConfig(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('appid');
            $grid->column('appsecret');
            $grid->column('mchid');
            //$grid->column('apikey');
            //$grid->column('cert_pem');
            //$grid->column('key_pem');
        
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
        return Show::make($id, new WxappConfig(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('appid');
            $show->field('appsecret');
            $show->field('mchid');
            $show->field('apikey');
            //$show->field('cert_pem');
            //$show->field('key_pem');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new WxappConfig(), function (Form $form) {
            $data = $form->input();
            //$form->model()->where();
            $form->display('id');
            $form->text('hotel_id');
            $form->text('appid');
            $form->text('appsecret');
            $form->text('mchid');
            $form->text('apikey');
            $form->text('cert_pem');
            $form->text('key_pem');
            $form->saving(function (Form $form) {
                // 判断是否是新增操作
                if ($form->isCreating()) {
                    // 添加服务商到分账接受人
                    $hotel_id = $form->model()->hotel_id;
                    \App\Models\Hotel\ProfitsharingReceiver::addIsvReceiverToPay($hotel_id);
                }


                // 删除用户提交的数据
                //$form->deleteInput('title');

                // 中断后续逻辑
            });
            // 跳转并提示成功信息
            $form->saved(function (Form $form) {
                return $form->response()->success('保存成功')->refresh();
            });

        });
    }
}
