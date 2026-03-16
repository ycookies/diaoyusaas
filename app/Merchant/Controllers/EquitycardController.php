<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Equitycard;
use App\Models\Hotel\Equitycard as EquitycardModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;

// 权益卡管理
class EquitycardController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('权益卡管理')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    public function pageMain(){
        $data = [];
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('权益卡信息',$this->form(),true);
        $tab->addLink('待开票请求', admin_url('invoices-record'));
        $tab->addLink('历史开票记录', admin_url('invoices-record-history'));

        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Equitycard(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            //$grid->column('template_id');
            //$grid->column('user_id');
            $grid->column('name');
            $grid->column('code');
            $grid->column('attribute')->using(EquitycardModel::$attribute_arr)->help('（1季卡2半年卡3年卡）');
            $grid->column('type')->help('1平台,2门店');
            $grid->column('card_type');
            $grid->column('rebate');
            $grid->column('cost');
            //$grid->column('img');
            $grid->column('logo')->image();
            //$grid->column('privilege');
            //$grid->column('introduce');
            //$grid->column('agreement');
            //$grid->column('sort');
            //$grid->column('weixin_rate');
            $grid->column('status');
            $grid->column('is_add');
            $grid->column('day_num');
            $grid->column('total_num');
            //$grid->column('time');
            //$grid->column('uniacid');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new Equitycard(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('template_id');
            $show->field('user_id');
            $show->field('name');
            $show->field('code');
            $show->field('attribute');
            $show->field('type');
            $show->field('card_type');
            $show->field('rebate');
            $show->field('cost');
            $show->field('img');
            $show->field('logo');
            $show->field('privilege');
            $show->field('introduce');
            $show->field('agreement');
            $show->field('sort');
            $show->field('weixin_rate');
            $show->field('state');
            $show->field('is_add');
            $show->field('day_num');
            $show->field('total_num');
            $show->field('time');
            $show->field('uniacid');
            $show->field('create_time');
            $show->field('update_time');
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
        return Form::make(new Equitycard(), function (Form $form) {
            //$form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            //$form->text('template_id');
            $form->hidden('user_id')->value(Admin::user()->id);
            // type
            $form->hidden('type')->value(2);
            $form->hidden('card_type')->value(1);
            $form->text('name');
            $form->text('code');
            $form->radio('attribute')->options(EquitycardModel::$attribute_arr);
            $form->rate('rebate')->help('折扣：例：95（9.5折）');
            $form->number('cost');
            $form->number('day_num');
            $form->number('total_num');
            $form->image('img');
            $form->image('logo');
            $form->markdown('privilege');
            $form->markdown('introduce');
            $form->markdown('agreement');
            $form->number('sort')->help('最小最靠前');
            $form->switch('status')->help('1上架2下架');
            //$form->text('is_add');


            $form->disableHeader();
            $form->disableListButton();
            $form->disableViewButton();
            $form->disableViewCheck();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
        });
    }
}
