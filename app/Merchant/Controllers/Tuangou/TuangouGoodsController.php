<?php
namespace App\Merchant\Controllers\Tuangou;


use App\Models\Hotel\Tuangou\TuangouGoods;
use App\Models\Hotel\Goods\GoodsWarehouse;
use App\Models\Hotel\Help;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Form as widgetsForm;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Merchant\Controllers\Tuangou\Renderable\GoodsWarehouseTable;
use App\Merchant\Controllers\Tuangou\Renderable\HelpsTable;
use App\Merchant\Controllers\Tuangou\Renderable\MinWidget;
use App\Merchant\Controllers\Tuangou\Actions\GoodsDown;
use App\Merchant\Renderable\MinappGoodsQrcode;

// 列表
class TuangouGoodsController extends AdminController
{
    public $translation = 'tuangou-good';

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('全部')
            //->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(TuangouGoods::with('goods','goods.warehouse','goods.warehouse.cats'), function (Grid $grid) {
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            //$grid->number();
            $grid->column('id')->sortable();
            $grid->column('goods.warehouse.cats.name','分类');
            $grid->column('goods.warehouse.main_img','商品主图')->image('','100');
            $grid->column('goods.warehouse.goods_name','商品标题')->display(function (){
                return "<a href='".admin_url('goods/goods-warehouse?id='.$this->goods->warehouse->id)."' target='_blank'>".$this->goods->warehouse->goods_name."</a>";
            })->width(200);
            $grid->column('goods.price','售价');
            $grid->column('sorts','排序')->sortable()->help('数值越大越靠前')->editable();
            $grid->column('goods.goods_stock','库存');
            $grid->column('goods.status','状态')->using(TuangouGoods::Status_arr)->label(TuangouGoods::Status_label);
            $grid->column('is_sell_well','热销推荐')->switch();
            $grid->column('goods.goods_share_qrcode','小程序码')
                ->display('查看')
                ->modal(function (Grid\Displayers\Modal $modal) {
                    // 设置弹窗标题
                    $modal->title('小程序码');
                    // 自定义图标
                    $modal->icon('fa fa-qrcode');

                    //$card = new \Dcat\Admin\Widgets\Card(null, $this->id.'-'.$this->room_id);
                    $path = '/pages1/tuangou/detail?tuangou_goods_id='.$this->id;
                    return  MinappGoodsQrcode::make()->payload(['hotel_id'=> $this->hotel_id,'goods_id'=>$this->goods->id,'name'=>$this->goods->warehouse->goods_name,'path'=> $path,'goods_share_qrcode' => $this->goods->goods_share_qrcode]);
                });
            /*$grid->column('status');
            $grid->column('price');
            $grid->column('use_attr');
            $grid->column('attr_groups');
            $grid->column('goods_stock');
            $grid->column('virtual_sales');
            $grid->column('confine_count');
            $grid->column('pieces');
            $grid->column('forehead');
            $grid->column('freight_id');
            $grid->column('give_integral');
            $grid->column('give_integral_type');
            $grid->column('forehead_integral');
            $grid->column('forehead_integral_type');
            $grid->column('accumulative');
            $grid->column('individual_share');
            $grid->column('attr_setting_type');
            $grid->column('is_level');
            $grid->column('is_level_alone');
            $grid->column('share_type');
            $grid->column('sign');
            $grid->column('app_share_pic');
            $grid->column('app_share_title');
            $grid->column('is_default_services');
            $grid->column('sort');
            $grid->column('is_delete');
            $grid->column('payment_people');
            $grid->column('payment_num');
            $grid->column('payment_amount');
            $grid->column('payment_order');
            $grid->column('confine_order_count');
            $grid->column('is_area_limit');
            $grid->column('area_limit');
            $grid->column('form_id');*/


            $grid->column('created_at');
            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->append(new GoodsDown());
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id','团购商品ID')->width(3);
                $filter->equal('goods_id','产品ID')->width(3);
                $filter->like('goods.warehouse.goods_name','商品名称')->width(3);
        
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
        return Show::make($id, new TuangouGoods(), function (Show $show) {
            $show->field('id');
            /*$show->field('hotel_id');
            $show->field('goods_warehouse_id');
            $show->field('status');
            $show->field('price');
            $show->field('use_attr');
            $show->field('attr_groups');
            $show->field('goods_stock');
            $show->field('virtual_sales');
            $show->field('confine_count');
            $show->field('pieces');
            $show->field('forehead');
            $show->field('freight_id');
            $show->field('give_integral');
            $show->field('give_integral_type');
            $show->field('forehead_integral');
            $show->field('forehead_integral_type');
            $show->field('accumulative');
            $show->field('individual_share');
            $show->field('attr_setting_type');
            $show->field('is_level');
            $show->field('is_level_alone');
            $show->field('share_type');
            $show->field('sign');
            $show->field('app_share_pic');
            $show->field('app_share_title');
            $show->field('is_default_services');
            $show->field('sort');
            $show->field('is_delete');
            $show->field('payment_people');
            $show->field('payment_num');
            $show->field('payment_amount');
            $show->field('payment_order');
            $show->field('confine_order_count');
            $show->field('is_area_limit');
            $show->field('area_limit');
            $show->field('form_id');*/
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
        return Form::make(TuangouGoods::with('goods','goods.warehouse'), function (Form $form) {
            //$form->display('id');
            $strk = <<<JS
           var goods_warehouse_id =   $('input[name=goods_warehouse_id]').val();
$('.field_goods_goods_warehouse_id').val(goods_warehouse_id);
JS;

            $form->divider('基本信息');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('sorts')->help('数值越大越靠前')->default(50);
            $form->hidden('goods.hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('goods.goods_warehouse_id')->setElementClass('goods_goods_warehouse_id');
            $form->selectTable('goods_warehouse_id','选择商品')
                ->title('选择商品')
                ->btn('<div class="btn btn-info">选商品</div>')
                //->dialogWidth('50%') // 弹窗宽度，默认 800px
                ->from(GoodsWarehouseTable::make(['id'=>$form->getKey()]))
                ->onHide($strk) // 设置渲染类实例，并传递自定义参数
                ->model(GoodsWarehouse::class, 'id', 'goods_name')
                ->help('<a href="'.admin_url('/goods/goods-warehouse').'" >还未添加商品？点击前往</a>')->required(); // 设置编辑数据显示
            //$form->text('goods.warehouse.id','商品名称');
            //$form->text('goods.warehouse.goods_name','商品名称')->setElementClass('goods_name')->readOnly();
            //$form->text('goods.warehouse.original_price','原价')->setElementClass('original_price')->readOnly();

            /*$form->text('goods.app_share_title','自定义分享标题')
                ->help(MinWidget::viewDiyTitleDemoImg());
            $form->image('goods.app_share_pic','自定义分享图片')
                ->width(3)->disk('oss')
                ->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg,webp', 'image/*')
                ->saveFullUrl()
                ->autoUpload()
                ->help(MinWidget::viewDiyShareDemoImg());*/

            $form->switch('goods.status','上架状态');
            $form->divider('价格库存');
            $form->number('goods.goods_stock','总库存')->default(100)->required();
            $form->currency('goods.price','销售价')->setElementClass('goods_price')->symbol('￥')->required();
            $form->divider('商品服务');
            $form->hidden('goods.is_default_services')->value(1);
            /*$form->checkbox('is_default_services','商品服务')
                ->options(['1'=> '默认服务']);*/
            $form->selectTable('goods.purchase_notices_art_id','购买须知')
                ->title('选择购买须知项')
                ->dialogWidth('90%') // 弹窗宽度，默认 800px
                ->from(HelpsTable::make()) // 设置渲染类实例，并传递自定义参数
                ->model(Help::class, 'id', 'title')->required(); // 设置编辑数据显示


            $form->selectTable('goods.service_guarantees_art_id','服务保障')
                ->title('选择 服务保障 条款')
                ->dialogWidth('90%') // 弹窗宽度，默认 800px
                ->from(HelpsTable::make()) // 设置渲染类实例，并传递自定义参数
                ->model(Help::class, 'id', 'title')->required(); // 设置编辑数据显示

            $form->divider('团购设置');
            $form->switch('goods.is_level','是否享受会员价购买')
                ->help('开启后(开卡会员，权益卡会员享受优惠折扣)');
            $form->switch('is_sell_well','是否热销')->help('热销会上首页展示');
            $form->hidden('is_alone_buy')->value(1);
            $form->hidden('is_alone_buy')->value(1);
            $form->hidden('end_time')->value('2025-11-30 15:52:11');
            $form->hidden('groups_restrictions')->value(-1);

            //$form->switch('is_alone_buy','是否允许单独购买')->default(1)->required();

            //$form->datetime('end_time','团购结束时间')->default('2024-11-30 15:52:11')->required();
            //$form->number('groups_restrictions','团购次数限制')->value(-1)->help('默认 -1,不限制');


            /* $form->checkbox('groups_restrictions','团购次数限制')
                ->options([1=>'无限制'])
                ->when(1, function (Form $form) {
                    $form->number('groups_restrictions','次数限制');
                })
                ->saving(function ($value) {return json_encode($value);});*/
            // 在表单提交前调用
            $form->submitted(function (Form $form) {
                // 获取用户提交参数

                // 上面写法等同于
                //$title = $form->input('title');

                // 删除用户提交的数据
                $form->deleteInput('goods.warehouse.goods_name');
                $form->deleteInput('goods.warehouse.original_price');

                // 中断后续逻辑
                //return $form->response()->error('在表单提交前调用')->data($form->input());
            });
            // 保存前
            $form->saving(function (Form $form) {
                // 判断是否是新增操作
                if ($form->isCreating()) {

                }

                // 删除用户提交的数据
                $form->deleteInput('title');

                // 中断后续逻辑
                //return $form->response()->error('服务器出错了~');
            });
            //$form->selectTable(GoodsWarehouseTable::make())->label('选择商品');
        });
    }

}
