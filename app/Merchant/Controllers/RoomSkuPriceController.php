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

use App\Merchant\Renderable\RoomSkuGiftTable;
use App\Models\Hotel\Room;
use App\Models\Hotel\RoomSkuGift;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomSkuTag;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use App\Merchant\Renderable\MinappRoomSkuQrcode;

// 房型房价列表
class RoomSkuPriceController extends AdminController {

    public $room_id;
    public $room_info;

    /**
     * page index
     */
    public function index(Content $content) {
        $header_title  = '房型房价管理';
        $room_id       = request('room_id');
        $this->room_id = $room_id;
        if (!empty($room_id)) {
            $room_info       = Room::where(['hotel_id' => Admin::user()->hotel_id, 'id' => $room_id])->first();
            $this->room_info = $room_info;
            $header_title    = $room_info->name . '';
        }

        return $content
            ->header($header_title)
            ->description('房价管理')
            ->breadcrumb(['text' => '房型管理', 'url' => '/room'], ['text' => '房价列表', 'uri' => ''])
            ->body($this->grid());
    }

    public function scriptk()
    {
        return <<<JS
        
JS;

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        Admin::script($this->scriptk());
        return Grid::make(RoomSkuPrice::with('room'), function (Grid $grid) {
            $grid->model()->setConstraints([
                'room_id' => $this->room_id,
            ])->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            //$grid->column('room.name','房型');
            //$grid->fixColumns(3, -1);
            $grid->column('id','SKU-ID');
            $grid->column('roomsku_title', '房型')->display(function () {
                return '<span class="text-muted">' . $this->room->name . '</span><br/> <' . $this->roomsku_title . '><br/><span class="text-muted">' . $this->sku_code . '</span>';
            })->width('140');
            $grid->column('roomsku_zaocan', '早餐');
            //$grid->column('roomsku_where_str','享受条件')->width('140')->explode(',')->badge();
            $grid->column('roomsku_gift', '礼包')
                ->if(function () {
                    return empty($this->roomsku_gift);
                })
                ->display('无')
                ->else()
                ->display('查看')
                ->expand(function () {
                    return \App\Merchant\Renderable\RoomGridSkuGiftTable::make()->payload(['gift_id' => $this->roomsku_gift]);
                });
            $grid->column('roomsku_tags', '享受服务')
                ->if(function () {
                    return empty($this->roomsku_tags);
                })
                ->display('无')
                ->else()
                ->display('查看')
                ->expand(function () {
                    return \App\Merchant\Renderable\RoomSkuTagsTable::make()->payload(['tags_id' => $this->roomsku_tags]);
                });
            //$grid->column('roomsku_give_points', '赚送积分');
            //$grid->column('roomsku_give_coupon', '赚送优惠券');
            $grid->column('roomsku_price', '日常销售价')->editable();
            $grid->column('roomsku_stock', '房量')->editable();
            $grid->column('state', '状态')
                ->switchGroup([
                    'recommend'    => '推荐',
                    'state'        => '展示',
                    'is_full_room' => '满房',
                ]);
            $grid->column('roomsku_qrcode','小程序码')
                ->display('查看')
                ->modal(function (Grid\Displayers\Modal $modal) {
                // 设置弹窗标题
                $modal->title('小程序码');
                // 自定义图标
                $modal->icon('fa fa-qrcode');

                //$card = new \Dcat\Admin\Widgets\Card(null, $this->id.'-'.$this->room_id);
                $path = '/pages2/hotel/room_detail?room_id='.$this->room_id.'&room_sku_id='.$this->id;
                //$path = 'pages2/hotel/room_detail';
                $scene = 'room_id='.$this->room_id.'&room_sku_id='.$this->id;
                return  MinappRoomSkuQrcode::make()->payload(['hotel_id'=> $this->hotel_id,'name'=>$this->room->name.'-'.$this->roomsku_title,'scene'=>$scene, 'path'=> $path,'roomsku_qrcode' => $this->roomsku_qrcode]);
            });
            /*$grid->column('recommend','推荐')->switch();
            $grid->column('state','状态')->switch();
            $grid->column('is_full_room','满房状态');*/
            $grid->disableBatchDelete();
            //$grid->disableRowSelector();
            //$grid->column('created_at');
            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                // 去掉编辑
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand(false);
                $filter->equal('room_id', '房型')->select(Room::where(['hotel_id' => Admin::user()->hotel_id])->pluck('name', 'id'))->width(4);
                $filter->like('roomsku_title', '销售标题')->width(3);

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
    protected function detail($id) {
        return Show::make($id, new RoomSkuPrice(), function (Show $show) {
            $show->field('id');
            $show->field('room_id');
            $show->field('roomsku_title');
            $show->field('roomsku_where');
            $show->field('roomsku_gift');
            $show->field('roomsku_tags');
            $show->field('roomsku_give_points');
            $show->field('roomsku_give_coupon');
            $show->field('roomsku_price');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        return Form::make(RoomSkuPrice::with('room'), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('sku_code')->value(Admin::user()->hotel_id . rand(1000, 9999));
            if (!empty(request('room_id'))) {
                $form->hidden('room_id')->value(request('room_id'));
                $room_name = Room::where(['id' => request('room_id')])->value('name');
                $form->html("<h3>$room_name</h3>")->label('房型名称');
            } else {
                $form->select('room_id', '选择房型')->options(Room::where(['hotel_id' => Admin::user()->hotel_id])->pluck('name', 'id'))->required();
            }
            $room_sku = [
                'roomsku_title' => '', // 销售标题
                'roomsku_price' => '', // 日常销售价
                'roomsku_stock' => '', // 房量
                'roomsku_zaocan' => '', // 早餐
                'roomsku_tags' => '', //  权益标签
                'roomsku_gift' => '',
            ];

            $form->text('roomsku_title', '销售标题')->required();
            $form->text('roomsku_price', '日常销售价')->prepend('￥')->width(3)->required();
            $form->text('roomsku_stock', '房量')->width(3)->append('间')->required();
            $form->radio('roomsku_zaocan', '早餐')->options([0 => '无早餐', 1 => '1份早餐', 2 => '2份早餐', 3 => '3份早餐'])->required();
            // 新增时
            //$form->sku('roomsku_where', '享受服务')->addColumn([]);
            /*$form->keyValue('roomsku_fuwu', '享受服务')
                ->setKeyLabel('服务名')
                ->setValueLabel('服务描述');*/
            $form->multipleSelectTable('roomsku_gift')
                ->title('套餐礼包')
                ->max(4)
                ->from(RoomSkuGiftTable::make())
                ->model(RoomSkuGift::class, 'id', 'sku_gift_name');
            $form->checkbox('roomsku_tags', '权益标签')
                ->options(RoomSkuTag::all()->pluck('sku_tags_name', 'id'))
                ->canCheckAll();
            //$form->text('roomsku_give_points')->prepend('')->append('个')->width(5)->help('选择一种优惠券,订房入住结束后,立即发放');
            //$form->select('roomsku_give_coupon')->help('订房入住结束后，赠送积分,可用积分换实物');
            $form->switch('recommend', '推荐')->help('推荐后,排名会靠前');
            $form->switch('state', '状态')->default(1)->help('是否展示');
            $form->switch('is_full_room', '满房状态')->help('关闭后,会展示满房图标,不可预订');

            /*$grid->column('recommend','推荐')->switch();
            $grid->column('state','状态')->switch();
            $grid->column('is_full_room','满房状态');*/
            $form->display('created_at');
            $form->saved(function (Form $form, $result) {
                // 判断是否是新增操作
                return $form->response()->success('操作成功') > redirect('/merchant/room-sku-price?room_id=' . $form->room_id);
            });

        });
    }
}
