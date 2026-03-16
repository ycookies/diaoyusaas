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

use App\Models\Hotel\InvoiceRegister;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class InvoiceRegisterController extends AdminController
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
        return Grid::make(new InvoiceRegister(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('saler_nuonuo_psd');
            $grid->column('salerName');
            $grid->column('salerTaxNum');
            $grid->column('salerAccount');
            $grid->column('salerAddress');
            $grid->column('invoiceLine');
            $grid->column('clerk');
            $grid->column('checker');
            $grid->column('payee');
            $grid->column('extensionNumber');
            $grid->column('departmentId');
            $grid->column('clerkId');
            $grid->column('InterCityIndicator');
            $grid->column('PropertyOwnershipCertificate');
            $grid->column('AreaUnit');
            $grid->column('region');
            $grid->column('salerAddress_as');
            $grid->column('invoiceLine_as');
            $grid->column('is_shudian');
            $grid->column('specificFactor');
            $grid->column('favouredPolicyFlag');
            $grid->column('goodCode');
            $grid->column('goodName');
            $grid->column('taxRate');
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
        return Show::make($id, new InvoiceRegister(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('saler_nuonuo_psd');
            $show->field('salerName');
            $show->field('salerTaxNum');
            $show->field('salerAccount');
            $show->field('salerAddress');
            $show->field('invoiceLine');
            $show->field('clerk');
            $show->field('checker');
            $show->field('payee');
            $show->field('extensionNumber');
            $show->field('departmentId');
            $show->field('clerkId');
            $show->field('InterCityIndicator');
            $show->field('PropertyOwnershipCertificate');
            $show->field('AreaUnit');
            $show->field('region');
            $show->field('salerAddress_as');
            $show->field('invoiceLine_as');
            $show->field('is_shudian');
            $show->field('specificFactor');
            $show->field('favouredPolicyFlag');
            $show->field('goodCode');
            $show->field('goodName');
            $show->field('taxRate');
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
        return Form::make(new InvoiceRegister(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('saler_nuonuo_psd');
            $form->text('salerName');
            $form->text('salerTaxNum');
            $form->text('salerAccount');
            $form->text('salerAddress');
            $form->text('invoiceLine');
            $form->text('clerk');
            $form->text('checker');
            $form->text('payee');
            $form->text('extensionNumber');
            $form->text('departmentId');
            $form->text('clerkId');
            $form->text('InterCityIndicator');
            $form->text('PropertyOwnershipCertificate');
            $form->text('AreaUnit');
            $form->text('region');
            $form->text('salerAddress_as');
            $form->text('invoiceLine_as');
            $form->text('is_shudian');
            $form->text('specificFactor');
            $form->text('favouredPolicyFlag');
            $form->text('goodCode');
            $form->text('goodName');
            $form->text('taxRate');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
