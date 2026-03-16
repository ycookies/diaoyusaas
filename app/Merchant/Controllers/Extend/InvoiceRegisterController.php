<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers\Extend;

use App\Models\Hotel\InvoiceRegister;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Services\NuonuoService;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Modal;
// 列表
class InvoiceRegisterController extends AdminController
{
    public $service;
    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('电子发票')
            ->description('数电发票-消费开票高效便捷')
            ->breadcrumb(['text'=>'电子发票','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $service = new NuonuoService();
        $grid =  Grid::make(new InvoiceRegister(), function (Grid $grid) use($service) {

            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            //$grid->column('saler_nuonuo_psd');
            $grid->column('salerName');
            $grid->column('salerTaxNum');
            $grid->column('invoiceLine')->label();
            $grid->column('taxRate');
            /*$grid->column('salerAccount');
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
            $grid->column('taxRate');*/
            $grid->disableRowSelector();
            $grid->disableFilterButton();

            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                //$actions->disableView();
                $form = Form::make(new InvoiceRegister());
                //$form->confirm('发送审核通知邮件？');
                $form->action('extend/emailToNuonuo');
                $form->html('<h3>通知数电开户审核方</h3>');
                $form->disableEditingCheck();
                $form->disableCreatingCheck();
                $form->disableViewCheck();
                $modal = Modal::make()
                    ->lg()
                    ->title('发送审核通知邮件')
                    ->body($form)
                    ->button('<i class="fa fa-mail-forward tips" data-title="发送审核通知邮件"></i>');
                $actions->append($modal);
            });
            $grid->column('is_oauth','授权情况')
                ->using(InvoiceRegister::Is_oauth_arr)
                ->label(InvoiceRegister::Is_oauth_label)
                ->if(function () {
                    return $this->is_oauth == 0;
                })->append('  <a href="'.$service->getOauthUrl(143).'" target="_blank"> >>> 去授权</a>');
            $grid->column('status','申请状态')
                ->using(InvoiceRegister::Status_arr)->label(InvoiceRegister::Status_arr_label);
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });

        $alert = Alert::make('<ul><li>1.新增电票开户申请</li><li>2.等待平台方审核完成注册</li><li>3.使用完成开户反馈回来的[账密]去授权</li><li>4.可正常开具数电电子发票</li></ul>', '开通数电发票步骤');
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
        return Show::make($id, new InvoiceRegister(), function (Show $show) {
            $show->field('id');
            //$show->field('hotel_id');
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
        return Form::make(new InvoiceRegister(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->default(Admin::user()->hotel_id);
            //$form->text('saler_nuonuo_psd');
            $form->text('salerName')->help('上海XXXX有限公司')->required();
            $form->text('salerTaxNum')->help('例：91310106798925642J')->required();
            $form->text('salerAccount')->help('例：工商银行 上海银行市政大厦支行 03003692239')->required();
            $form->text('salerAddress')->help('上海市延安中路1000号22160016')->required();
            $form->hidden('invoiceLine')->value('数电发票（全电发票）无设备');
            $form->text('clerk')->help('')->required();
            $form->text('checker')->help('数电发票可不填写');
            $form->text('payee')->help('数电发票可不填写');
            $form->text('extensionNumber')->help('数电发票默认分机号：666')->default('666')->required();
            /*$form->text('departmentId');
            $form->text('clerkId');*/
            $form->text('InterCityIndicator')->default('否')->help('数电必填：商户填写：默认为否');
            $form->text('PropertyOwnershipCertificate')->default('否')->help('数电必填：商户填写：默认为否');
            $form->text('AreaUnit')->default('否')->help('数电必填：商户填写：默认为否');
            $form->text('region')->help('数电必填：商户填写：如：广东省深圳市龙华区');
            //$form->distpicker(['province_id', 'city_id', 'district_id'], '经营场所在地')->required();
            //$form->text('salerAddress_as');
            $form->text('invoiceLine_as')->default('数电普票')->help('数电必填：商户填写；默认PC');
            $form->switch('is_shudian')->default(1)->help('数电必填：商户填写');
            $form->text('specificFactor')->default(6)->help('数电必填：航信填写：默认为6');
            $form->text('favouredPolicyFlag')->default('按5%简易征收')->help('数电必填：平台填写，商户确认：默认为填写：按5%简易征收');
            $form->text('goodCode')->help('税收分类编码')->help('例：3040502020200000000')->required();
            $form->text('goodName')->help('开票商品的名称')->help('例：酒店住宿费')->required();
            $form->text('taxRate')->help('商品对应的税率')->required();
            $form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
