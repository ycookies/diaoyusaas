<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\InvoiceRegist;
use App\Models\Hotel\InvoiceRegist as  InvoiceRegistModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Admin;
class InvoiceRegistController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('发票信息')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->pageMain());
    }

    public function pageMain(){
        $data = [];
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中

        $tab->addLink('待开票请求', admin_url('invoices-record'));
        $tab->addLink('历史开票记录', admin_url('invoices-record-history'));
        //$tab->add('企业开票信息',$this->form(),true);
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    public function tab2(){
        return '123';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new InvoiceRegist(), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');

            $grid->column('enterpriseName');
            $grid->column('legalPersonName');
            $grid->column('taxpayerNum');
            $grid->column('regionCode');
            $grid->column('seriaFour');
            $grid->column('secret_key');
            $grid->column('platformCode');
            $grid->column('platformCode_p');
            $grid->column('contactsName');
            $grid->column('contactsEmail');
            $grid->column('contactsPhone');
            $grid->column('sellerTel');
            $grid->column('reviewerName');
            $grid->column('casherName');
            $grid->column('sellerBankName');
            $grid->column('sellerBankAccount');
            $grid->column('cityName');
            $grid->column('enterpriseAddress');
            $grid->column('taxRegistrationCertificate');
            $grid->column('isPermitPaperInvoice');
            $grid->column('status');
            $grid->column('invoiceNum');
            $grid->column('PaperInvoiceNum');
            $grid->column('registrationCode');
            $grid->column('authorizationCode');
            $grid->column('type');
            $grid->column('send_msg');
            $grid->column('Warning_number');
            $grid->column('is_box');
            $grid->column('invoice_status');
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
        return Show::make($id, new InvoiceRegist(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('enterpriseName');
            $show->field('legalPersonName');
            $show->field('taxpayerNum');
            $show->field('regionCode');
            $show->field('seriaFour');
            $show->field('secret_key');
            $show->field('platformCode');
            $show->field('platformCode_p');
            $show->field('contactsName');
            $show->field('contactsEmail');
            $show->field('contactsPhone');
            $show->field('sellerTel');
            $show->field('reviewerName');
            $show->field('casherName');
            $show->field('sellerBankName');
            $show->field('sellerBankAccount');
            $show->field('cityName');
            $show->field('enterpriseAddress');
            $show->field('taxRegistrationCertificate');
            $show->field('isPermitPaperInvoice');
            $show->field('status');
            $show->field('invoiceNum');
            $show->field('PaperInvoiceNum');
            $show->field('registrationCode');
            $show->field('authorizationCode');
            $show->field('type');
            $show->field('send_msg');
            $show->field('Warning_number');
            $show->field('is_box');
            $show->field('invoice_status');
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
        return Form::make(new InvoiceRegist(), function (Form $form) {
            $form->disableHeader();
            $form->disableViewCheck();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('enterpriseName');
            $form->text('legalPersonName');
            $form->text('taxpayerNum');
            $form->text('regionCode');
            $form->text('seriaFour');
            $form->text('secret_key');
            $form->text('platformCode');
            $form->text('platformCode_p');
            $form->text('contactsName');
            $form->text('contactsEmail');
            $form->text('contactsPhone');
            $form->text('sellerTel');
            $form->text('reviewerName');
            $form->text('casherName');
            $form->text('sellerBankName');
            $form->text('sellerBankAccount');
            $form->text('cityName');
            $form->text('enterpriseAddress');
            $form->text('taxRegistrationCertificate');

            $form->text('invoiceNum');
            $form->text('PaperInvoiceNum');
            $form->text('registrationCode');
            $form->text('authorizationCode');
            $form->text('type');

            $form->text('Warning_number');
            $form->select('status')->options(InvoiceRegistModel::$status_arr)->width(150)->help('1审核中,2后台审核,3通过票宝通审核');
            $form->switch('is_box')->help('1预警 0不预警');
            $form->switch('isPermitPaperInvoice')->help('1支持开纸票，0不支持');
            $form->switch('send_msg')->help('1开启,0未开启 ');
            $form->switch('invoice_status')->help('1开启,0关闭');
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
