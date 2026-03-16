<?php

namespace App\Merchant\Controllers;

use App\Models\Hotel\MemberOrder;
use App\Models\Hotel\MemberVipSet;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Widgets\Alert;

// 超级vip会员卡
class MemberVipSetController extends AdminController
{

     /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('会员购买 管理')
            ->description('全部')
            ->breadcrumb(['text'=>'会员购买 管理','uri'=>''])
            ->body($this->grid());
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid =  Grid::make(new MemberVipSet(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','ASC');
            //$grid->column('id');
            //$grid->column('mall_id');
            //$grid->column('level','会员卡等级');
            $grid->column('flag');
            $grid->column('name','会员卡名称');
            //$grid->column('auto_update');
            //$grid->column('money');
            $grid->column('pic_url','会员卡图标')->image('','122px')->width('122px');
            $grid->column('bg_pic_url','背景图片')->image('','100px')->width('100px');
            $grid->column('price','购买价格(元)');
            $grid->column('unit','单位');
            $grid->column('discount','订房折扣')->display(function (){
                return bcmul($this->discount,0.1,1).'折';
            });
            $grid->column('reward_points','奖励积分');
            //$grid->column('pic_url');
            //$grid->column('is_purchase');

            //$grid->column('rules');
            //$grid->column('bg_pic_url');
            $grid->column('status','启用状态')->switch();
            //$grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->disableRowSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
            $grid->wrap(function (Renderable $view){
                $tab = Tab::make();
                $tab->add('超级VIP会员卡', $view);
                $tab->addLink('购买订单',admin_url('member-order'));
                return $tab;
            });
        });
        $htmll = <<<HTML
<ol>
    <li>超级VIP会员卡，有时间期限，在有效使用期限内, 可享受相对应的权益。</li>
    <li>此超级VIP会员卡，只可付费购买获得。不可自动升级。</li>
    <li><span class="text-danger">在订房时，优先使用超级VIP权益</span></li>
</ol>
HTML;
        $alert = Alert::make($htmll, '提示:');
        return $alert->info().$grid;
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
        return Show::make($id, new MemberVipSet(), function (Show $show) {
            $show->field('id');
            //$show->field('mall_id');
            $show->field('level');
            $show->field('flag');
            $show->field('name');
            $show->field('auto_update');
            $show->field('money');
            $show->field('discount');
            $show->field('status');
            $show->field('pic_url');
            $show->field('is_purchase');
            $show->field('price');
            $show->field('vip_days');
            $show->field('rules');
            $show->field('is_delete');
            $show->field('bg_pic_url');
            $show->field('created_at');
            //$show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(MemberVipSet::with('right'), function (Form $form) {
            //$form->display('id')->width(3);
            //$form->text('mall_id');
            //$form->select('level')->options(MemberVipSet::LevelArr)->required();
            $form->width(8, 3);
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('flag')->required();
            $form->text('name')->required();
            $form->photo('pic_url','会员微标')
                ->disk('hotel_'.Admin::user()->hotel_id)
                ->accept('jpg,png,gif,jpeg')
                ->help('图片尺寸:101*32')
                ->nametype('uniqid')
                ->saveFullUrl(true)
                ->remove(true)->required();

            /*$form->image('pic_url')->width(3)
                ->help('图片尺寸:102*32')
                ->dimensions(['width' => 102, 'height' => 32])
                ->disk('admin')
                ->saveFullUrl()
                ->removable(false)
                ->autoUpload()
                ->uniqueName()
                ->required();*/
            /*$form->image('bg_pic_url','背景图片')
                ->width(3)
                ->help('图片尺寸:253*132')
                ->dimensions(['width' => 253, 'height' => 132])
                ->disk('admin')
                ->saveFullUrl()
                ->removable(false)
                ->autoUpload()
                ->uniqueName()
                ->required();*/
            $form->photo('bg_pic_url','背景图片')
                //->dimensionsk(['width' => 253, 'height' => 132])
                ->disk('hotel_'.Admin::user()->hotel_id)
                ->accept('jpg,png,gif,jpeg')

                ->help('图片尺寸:253*132')
                ->nametype('uniqid')
                ->saveFullUrl(true)
                ->remove(true)->required();
            $form->text('price')->required();
            //$form->text('vip_days','有效天数')->help('单位为天,365天为1年')->required();
            $form->radio('unit','有效单位')->options(MemberVipSet::Unit_arr);
            $form->hasMany('right', '相关权益描述(多条)', function (Form\NestedForm $form) {
                $form->row(function (Form\Row $form) {
                    $form->width(3)->text('title', '权益标题')->required();
                    $form->width(3)->text('content', '权益内容')->required();
                    $form->width(4)->iconimg('pic_url','权益图标')
                        ->disk('hotel_'.Admin::user()->hotel_id)
                        ->accept('jpg,png,jpeg')
                        ->help('图标尺寸:32*32,格式：jpg,png,jpeg')
                        ->nametype('datetime')
                        ->saveFullUrl(true)
                        ->remove(true);

                });
            });
            $form->divider();
            $form->switch('status','是否启用');
            $form->rate('discount','权益订房折扣')
                ->rules('required|numeric|between:10,100', [
                    'required' => '请填写优惠折扣',
                    'numeric' => '只能是纯数字',
                    'between'   => '只能是10-100之间的数值',
                ])
                ->help('范围:10-100.例：98，就是9.8折优惠')->required();
            $form->number('reward_points','赠送积分')
                ->help('默认：10，赚送10个积分');
            $form->editor('rules','权益说明')->required();
            //$form->display('created_at');
        });
    }
}
