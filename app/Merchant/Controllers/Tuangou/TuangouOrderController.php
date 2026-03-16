<?php
namespace App\Merchant\Controllers\Tuangou;


use App\Models\Hotel\Goods\Good;
use App\Models\Hotel\Order\Order as mOrder;
use App\Models\Hotel\Order\OrderDetail;
use App\Models\Hotel\Tuangou\TuangouOrder;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Merchant\Controllers\Tuangou\Actions\DaodianHexiao;
use App\Merchant\Controllers\Tuangou\Forms\DaodianHexiaoForm;
use App\Merchant\Controllers\Tuangou\Forms\HandleRefundForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Card;
use Illuminate\Support\Str;
use Dcat\Admin\Widgets;
use App\Models\Hotel\BookingOrder;
// 列表
class TuangouOrderController extends AdminController
{

    public $orderinfo;

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('团购订单管理')
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
         $grid =  Grid::make(mOrder::with('user','tuangouorder','refund','goods','detail','clerk'), function (Grid $grid) {
            $grid->model()->where(['sign'=> 'tuangou','hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->number();
            $grid->export();
            $grid->column('order_no','订单编号');
            //$grid->column('detail.goods_info.warehouse.main_img','图片')->image();
            $grid->column('detail.goods_info','商品信息')->display(function ($goods_info){
                //$goods_info = json_decode($goods_info,true);

                return "<img class='img img-thumbnail' data-action='preview-img' src='".$goods_info['warehouse']['main_img']."' width='100' class=''/> <a href='".admin_url('/tuangou/goods?goods_id='.$this->detail->goods_id)."' target='_blank'><div class='text-muted tips' data-title='".$goods_info['warehouse']['goods_name']."'>".Str::limit($goods_info['warehouse']['goods_name'], 20,'...')."</div></a";

            });
            $grid->column('user.nick_name','用户');
             $grid->column('detail.num','购买数量');
            $grid->column('total_pay_price','交易金额');
            $grid->column('profitsharing_after_price','分账后金额');
            //$grid->column('is_pay','支付状态')->using( mOrder::Is_pay_arr);
            $grid->column('tuangouorder.order_status','订单状态')->using(TuangouOrderRelation::Order_status_arr)->label();
            $grid->column('clerk_id','是否核销')->display(function (){
                if($this->clerk_id != ''){
                    return '是';
                }
                return '-';
            });
            $grid->column('created_at');
            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableBatchActions();
            $grid->tools(Modal::make()->title('到店核销')
                ->body(DaodianHexiaoForm::make())->button('<button class="btn btn-primary">到店核销</button>'));
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                //  核销
                if(!empty($actions->row->refund->id)){
                    $title = '处理退款';
                    if($actions->row->refund->status != 1){
                        $title = '查看退款';
                    }
                    $modal = Modal::make($title)->lg();
                    $modal->body(HandleRefundForm::make()->payload($actions->row->toArray()));
                    $modal->button($title);
                    $actions->append($modal->render());
                }

                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand(false);
                $filter->like('order_no','订单编号')->width(3);
                $filter->equal('tuangouorder.order_status','订单状态')->select(TuangouOrderRelation::Order_status_arr)->width(3);
                /*$filter->scope('is_pay', '未支付')->where('is_pay', 0);
                $filter->scope('is_pay', '已支付')->where('is_pay', 1);
                $filter->scope('clerk_id', '未核销')->where('clerk_id', null);
                $filter->scope('clerk_id', '已核销')->where([['clerk_id', '<>',null]]);*/

        
            });
             $tab = \Dcat\Admin\Widgets\Tab::make();
             //$tab->vertical();
             $request = request();
             $order_status_active = '';
             $tuangouorder_where = $request->get('tuangouorder');
             if(!empty($tuangouorder_where)){
                 $order_status_active = $tuangouorder_where['order_status'];
             }
             $tab->addLink('全部', admin_url('tuangou/order'),empty($order_status_active) ? true:false);
             $tab->addLink('待付款', admin_url('tuangou/order').'?tuangouorder%5Border_status%5D=1',$order_status_active == 1 ? true:false);
             $tab->addLink('未核销', admin_url('tuangou/order').'?tuangouorder%5Border_status%5D=2',$order_status_active == 2 ? true:false);
             $tab->addLink('已完成', admin_url('tuangou/order').'?tuangouorder%5Border_status%5D=3',$order_status_active == 3 ? true:false);
             $tab->addLink('已评价', admin_url('tuangou/order').'?tuangouorder%5Border_status%5D=4',$order_status_active == 4 ? true:false);
             $tab->addLink('已退款', admin_url('tuangou/order').'?tuangouorder%5Border_status%5D=5',$order_status_active == 5 ? true:false);
             $grid->header($tab->render());
        });

         return \Dcat\Admin\Widgets\Card::make('',$grid);
    }

    /*public function show($id, Content $content)
    {
        Admin::style(<<<CSS
        th{color:#585858 !important;}
        td{color:#9f9b9b !important;} 
CSS
        );

        $orderinfo = mOrder::with('user','goods','detail','clerk')->where(['id'=> $id,'hotel_id'=> Admin::user()->hotel_id])->first();
        $this->orderinfo = $orderinfo;

        return  $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['show'] ?? trans('admin.show'))
            ->row($this->crad1())
            ->row($this->crad2())
            ->row($this->crad3());


    }

    public function crad1(){
        $orderinfo = $this->orderinfo;
        $htmls = view('admin.full-order.progress-bar',compact('orderinfo'));
        $body = '订单号:<span class="f12 text-gray">'.$this->orderinfo->order_no.'</span> &nbsp;&nbsp;订单状态:<span class="f12 text-gray">'.BookingOrder::$status_arr[$this->orderinfo->status].'</span> &nbsp;&nbsp;支付方式:<span class="f12 text-gray">'.BookingOrder::$type_arr[$this->orderinfo->type].'</span>';
        $crad  = Widgets\Card::make($body,$htmls)->withHeaderBorder();
        return $crad->render();
    }

    public function crad2(){

    }
    public function crad3(){

    }*/

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id,mOrder::with('user','goods','detail','clerk'), function (Show $show) {

            $show->row(function (Show\Row $show) {
                $show->field('id');
                $show->field('order_no','订单编号');
                $show->field('trade_no','交易流水号');
                $show->field('detail.goods_info','商品信息')->as(function ($goods_info) {
                    //$goods_info = json_decode($goods_info,true);
                    return "<img class='img img-thumbnail' data-action='preview-img' src='".$goods_info['warehouse']['main_img']."' width='100'/> <div class='text-muted'>".$goods_info['warehouse']['goods_name']."</div>";

                })->unescape();

                $show->field('user.nick_name','购买用户');
                $show->field('detail.num','购买数量');
                $show->field('total_pay_price','交易金额');
                $show->field('is_pay','支付状态')->using(mOrder::Is_pay_arr)->unescape();
            });


        });
    }

}
