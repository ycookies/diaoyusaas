<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Portal\Controllers;

use App\Portal\Repositories\CaseTaskTrustOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class CaseTaskTrustOrderController extends AdminController
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
        return Grid::make(new CaseTaskTrustOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('case_id');
            $grid->column('out_trade_no');
            $grid->column('trade_no');
            $grid->column('pay_amount');
            $grid->column('business_type');
            $grid->column('business_id');
            $grid->column('pay_info');
            $grid->column('pay_time');
            $grid->column('status');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new CaseTaskTrustOrder(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('case_id');
            $show->field('out_trade_no');
            $show->field('trade_no');
            $show->field('pay_amount');
            $show->field('business_type');
            $show->field('business_id');
            $show->field('pay_info');
            $show->field('pay_time');
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
        return Form::make(new CaseTaskTrustOrder(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('case_id');
            $form->text('out_trade_no');
            $form->text('trade_no');
            $form->text('pay_amount');
            $form->text('business_type');
            $form->text('business_id');
            $form->text('pay_info');
            $form->text('pay_time');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
