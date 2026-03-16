<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\Invoicerecord;
use App\Models\Hotel\Invoicerecord as InvoicerecordModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Admin;

// 电票开具记录
class InvoicerecordController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('电票待开请求')
            ->description('全部')
            ->breadcrumb(['text'=>'电票待开请求','uri'=>''])
            ->body($this->pageMain());
    }

    public function pageMain(){
        $data = [];
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中

        $tab->add('待开票请求', $this->grid(),true);
        $tab->addLink('历史开票记录', admin_url('invoices-record-history'));
        //$tab->addLink('企业开票信息',admin_url('invoices'));
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }


    /**
     * page history
     */
    public function history(Content $content)
    {
        return $content
            ->header('电票开具历史记录')
            ->description('全部')
            ->breadcrumb(['text'=>'电票开具历史记录','uri'=>''])
            ->body($this->pageMainHistory());
    }

    public function pageMainHistory(){
        $data = [];
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->addLink('待开票请求', admin_url('invoices-record'));
        $tab->add('历史开票记录', $this->grid1(),true);
        //$tab->addLink('企业开票信息',admin_url('invoices'));
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid1()
    {
        return Grid::make(new Invoicerecord(), function (Grid $grid) {
            $grid->model()->where(['invoice_status'=>'success','hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('orderNo','开票订单编号');
            $grid->column('goodsInfo');
            $grid->column('goodsAmount');
            $grid->column('buyerName','发票抬头');
            $grid->column('buyerTaxNum','购买人纳税人识别号');
            $grid->column('buyerPhone','购买人手机');
            $grid->column('takerEmail','购买人邮箱');
            $grid->column('downloadUrl','发票')->display(function ($e){
                return '<a target="_blank" href="'.$this->downloadUrl.'"> 查看</a>';
            });
            /*$grid->column('buyerBankName');
            $grid->column('buyerBankAccount');
            $grid->column('buyerAddress');
            $grid->column('casherName');
            $grid->column('reviewerName');
            $grid->column('drawerName');
            $grid->column('takerName');
            $grid->column('takerTel');
            $grid->column('takerEmail');
            $grid->column('invoiceReqSerialNo');
            $grid->column('invoiceCode');
            $grid->column('invoiceNo');
            $grid->column('securityCode');
            $grid->column('invoiceNo_b');
            $grid->column('invoiceDate');
            $grid->column('noTaxAmount');
            $grid->column('taxAmount');
            $grid->column('invoicePdf');
            $grid->column('downloadUrl');
            $grid->column('is_from')->help('1后台商户开票 2后台商户生成二维码(带商品信息) 3 后台统一二维码开票');
            $grid->column('is_check');
            $grid->column('check_status');
            $grid->column('is_delete');

            $grid->column('status');
            $grid->column('invoice_type');
            $grid->column('type');
            $grid->column('user_id');
            $grid->column('remark');
            $grid->column('m_remark');
            $grid->column('qrCodePath');
            $grid->column('push_to_alipay');
            $grid->column('room_number');
            $grid->column('tradeNo');
            $grid->column('taxpayerNum');
            $grid->column('move_time');
            $grid->column('id_card');*/
            $grid->column('invoice_status')->using(InvoicerecordModel::$status_arr)->label()->help('success 开票成功 error 开票失败 wait 等待开票 wait_push 等待票宝通推送');
            $grid->column('created_at');
            $grid->quickSearch(['orderSerialNo'])->placeholder('入住人，入住人电话，订单编号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->export();
            //$grid->
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('invoice_status')->select(InvoicerecordModel::$status_arr);
            });
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Invoicerecord(), function (Grid $grid) {
            $grid->model()->where(['invoice_status'=>'wait','hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->column('orderNo','开票订单编号');
            $grid->column('goodsInfo','商品信息');
            $grid->column('goodsAmount','商品总价');
            $grid->column('buyerName','发票抬头');
            $grid->column('buyerTaxNum','购买人纳税人识别号');
            $grid->column('buyerPhone','购买人手机');
            $grid->column('takerEmail','购买人邮箱');
            /*$grid->column('buyerBankName');
            $grid->column('buyerBankAccount');
            $grid->column('buyerAddress');
            $grid->column('casherName');
            $grid->column('reviewerName');
            $grid->column('drawerName');
            $grid->column('takerName');
            $grid->column('takerTel');
            $grid->column('takerEmail');
            $grid->column('invoiceReqSerialNo');
            $grid->column('invoiceCode');
            $grid->column('invoiceNo');
            $grid->column('securityCode');
            $grid->column('invoiceNo_b');
            $grid->column('invoiceDate');
            $grid->column('noTaxAmount');
            $grid->column('taxAmount');
            $grid->column('invoicePdf');
            $grid->column('downloadUrl');
            $grid->column('is_from')->help('1后台商户开票 2后台商户生成二维码(带商品信息) 3 后台统一二维码开票');
            $grid->column('is_check');
            $grid->column('check_status');
            $grid->column('is_delete');

            $grid->column('status');
            $grid->column('invoice_type');
            $grid->column('type');
            $grid->column('user_id');
            $grid->column('remark');
            $grid->column('m_remark');
            $grid->column('qrCodePath');
            $grid->column('push_to_alipay');
            $grid->column('room_number');
            $grid->column('tradeNo');
            $grid->column('taxpayerNum');
            $grid->column('move_time');
            $grid->column('id_card');*/
            $grid->column('invoice_status')->using(InvoicerecordModel::$status_arr)->label()->help('success 开票成功 error 开票失败 wait 等待开票 wait_push 等待票宝通推送');
            $grid->column('created_at');
            $grid->quickSearch(['orderSerialNo'])->placeholder('入住人，入住人电话，订单编号');
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->export();
            //$grid->
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('invoice_status')->select(InvoicerecordModel::$status_arr);
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
        return Show::make($id, new Invoicerecord(), function (Show $show) {
            $show->field('id');
            $show->field('seller_id');
            $show->field('orderSerialNo');
            $show->field('goodsInfo');
            $show->field('goodsAmount');
            $show->field('buyerName');
            $show->field('buyerTaxpayerNum');
            $show->field('buyerTel');
            $show->field('buyerEmail');
            $show->field('buyerBankName');
            $show->field('buyerBankAccount');
            $show->field('buyerAddress');
            $show->field('casherName');
            $show->field('reviewerName');
            $show->field('drawerName');
            $show->field('takerName');
            $show->field('takerTel');
            $show->field('takerEmail');
            $show->field('invoiceReqSerialNo');
            $show->field('invoiceCode');
            $show->field('invoiceNo');
            $show->field('securityCode');
            $show->field('invoiceNo_b');
            $show->field('invoiceDate');
            $show->field('noTaxAmount');
            $show->field('taxAmount');
            $show->field('invoicePdf');
            $show->field('downloadUrl');
            $show->field('is_from');
            $show->field('is_check');
            $show->field('check_status');
            $show->field('is_delete');
            $show->field('invoice_status');
            $show->field('status');
            $show->field('invoice_type');
            $show->field('type');
            $show->field('user_id');
            $show->field('remark');
            $show->field('m_remark');
            $show->field('qrCodePath');
            $show->field('push_to_alipay');
            $show->field('room_number');
            $show->field('tradeNo');
            $show->field('taxpayerNum');
            $show->field('move_time');
            $show->field('id_card');
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
        return Form::make(new Invoicerecord(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('orderSerialNo');
            $form->text('goodsInfo');
            $form->text('goodsAmount');
            $form->text('buyerName');
            $form->text('buyerTaxpayerNum');
            $form->text('buyerTel');
            $form->text('buyerEmail');
            $form->text('buyerBankName');
            $form->text('buyerBankAccount');
            $form->text('buyerAddress');
            $form->text('casherName');
            $form->text('reviewerName');
            $form->text('drawerName');
            $form->text('takerName');
            $form->text('takerTel');
            $form->text('takerEmail');
            $form->text('invoiceReqSerialNo');
            $form->text('invoiceCode');
            $form->text('invoiceNo');
            $form->text('securityCode');
            $form->text('invoiceNo_b');
            $form->text('invoiceDate');
            $form->text('noTaxAmount');
            $form->text('taxAmount');
            $form->text('invoicePdf');
            $form->text('downloadUrl');
            $form->text('is_from');
            $form->text('is_check');
            $form->text('check_status');
            $form->text('is_delete');
            $form->text('invoice_status');
            $form->text('status');
            $form->text('invoice_type');
            $form->text('type');
            $form->text('user_id');
            $form->text('remark');
            $form->text('m_remark');
            $form->text('qrCodePath');
            $form->text('push_to_alipay');
            $form->text('room_number');
            $form->text('tradeNo');
            $form->text('taxpayerNum');
            $form->text('move_time');
            $form->text('id_card');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
