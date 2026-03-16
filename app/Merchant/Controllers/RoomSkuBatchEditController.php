<?php

namespace App\Merchant\Controllers;


use App\Http\Controllers\Controller;
use App\Merchant\Actions\Grid\MyAction;
use App\Models\Hotel\Room as RoomModel;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomTiaojiaLog;
use App\Services\RoomTiaojiaService;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Validator;

// 批量改房价
class RoomSkuBatchEditController extends Controller {
    public function index(Content $content) {
        return $content
            ->header('批量改房价')
            ->description('')
            ->breadcrumb(['text' => '批量改房价', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->addLink('房价日历', admin_url('room-sku-calendar'));
        $tab->addLink('房价设置', admin_url('room-sku-price-set'));
        $tab->add('批量调房价', $this->mainTabs(), 'true');
        return $tab->withCard();

    }

    // 页面
    public function mainTabs() {
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        //$tab->add('日期范围调价',$this->tab1(),'true');
        //$tab->add('节假日调价', $this->tab1(), 'true');
        $tab->add('调价日志', $this->tab3());
        return $tab->withCard();

    }

    public function tab1() {
        // 配
        $form = Form::make(new Roomprice());
        $form->width(10, 2);
        $form->action('room-sku-batch-edit-save');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('act_type', '操作项')->value('diy_jiejiari');
        $form->radio('batch_tiaojia_type', '节假日')
            ->when('0', function (Form $form) {
                $form->dateRange('startDate', 'endDate', '选择日期');
            })
            ->options(\App\Services\RoomSkuTiaojiaService::Batch_tiaojia_type_arr)->required();

        /*$form->checkbox('room_sku_ids', '选择房型')->options(RoomSkuPrice::where(['hotel_id' => Admin::user()->hotel_id])->pluck('roomsku_title', 'id'))
            ->canCheckAll()
            ->required();*/
        $form->radio('set_price', '调价方式')
            ->when('1', function (Form $form) {
                $form->currency('set_value1', '数值')->width('150');
            })
            ->when('2', function (Form $form) {
                $form->currency('set_value2', '数值')->width('150');
            })
            ->when('3', function (Form $form) {
                $form->rate('set_value3', '数值')->help('百分比')->width('150');
            })
            ->when('4', function (Form $form) {
                $form->rate('set_value4', '数值')->help('百分比')->width('150');
            })
            ->options(RoomTiaojiaLog::Set_price_type)
            ->required();
        $form->disableResetButton();
        //$card = Card::make('', $form);

        return $form;
    }

    // 调价日志
    public function tab3() {
        Admin::translation('room-tiaojia-log');

            $grid = Grid::make(new RoomTiaojiaLog(), function (Grid $grid) {
                $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
                //$grid->column('id');
                $grid->column('room_ids_txt', '相关房型')->width('100')->label();
                $grid->column('room_sku_ids_txt', '房型销售SKU')->width('100')->label();
                $grid->column('date_type', '节假日类型')->using(\App\Services\RoomSkuTiaojiaService::Batch_tiaojia_type_arr)->label();
                $grid->column('start_date', '开始日期')
                    ->if(function () {
                        return $this->date_type != '0';
                    })->prepend('每年国家法定');
                $grid->column('end_date', '结束日期')->if(function () {
                    return $this->date_type != '0';
                })->prepend('每年国家法定');
                $grid->column('set_price', '调价方式')->using(RoomTiaojiaLog::Set_price_type);
                $grid->column('set_value', '调价数值');
                $grid->column('status', '状态')->using(RoomTiaojiaLog::Status_arr);
                $grid->column('created_at', '调价时间');
                $grid->disableBatchDelete();
                $grid->disableCreateButton();
                //$grid->disableActions();

                // 设置新节假日调价

                /*$form = Form::make(new Roomprice());

                $form->width(10, 2);
                $form->action('room-sku-batch-edit-save');
                $form->confirm('确认已经填好了吗?');
                $form->hidden('act_type', '操作项')->value('diy_jiejiari');
                $form->radio('batch_tiaojia_type', '节假日')
                    ->when('0', function (Form $form) {
                        $form->dateRange('startDate', 'endDate', '选择日期');
                    })
                    ->options(RoomTiaojiaLog::Batch_tiaojia_type_arr)->required();
                $list = RoomSkuPrice::with('room')->where(['hotel_id' => Admin::user()->hotel_id])->select('id','room_id','roomsku_title')->get();

                $form->checkbox('room_sku_ids', '选择房型客房销售SKU')->options()
                    ->canCheckAll()
                    ->required();
                $form->radio('set_price', '调价方式')
                    ->when('1', function (Form $form) {
                        $form->currency('set_value1', '数值')->width('150');
                    })
                    ->when('2', function (Form $form) {
                        $form->currency('set_value2', '数值')->width('150');
                    })
                    ->when('3', function (Form $form) {
                        $form->rate('set_value3', '数值')->help('百分比')->width('150');
                    })
                    ->when('4', function (Form $form) {
                        $form->rate('set_value4', '数值')->help('百分比')->width('150');
                    })
                    ->options(RoomTiaojiaLog::Set_price_type)
                    ->required();
                $form->disableResetButton();*/
                $modal = Modal::make()
                    ->lg()
                    ->title('节假日调价')
                    ->body(\App\Merchant\Renderable\RoomSkuBatchTiaojiaForm::make())
                    ->button('<button class="btn btn-white btn-outline"> 节假日调价</button>');

                $grid->tools($modal);

                $grid->setActionClass(Grid\Displayers\Actions::class);
                $grid->actions(function ($actions) {
                    // 去掉删除
                    $actions->disableDelete();
                    // 去掉编辑
                    $actions->disableEdit();
                    $actions->disableView();
                    $actions->append(new MyAction());
                });
                //$grid->quickSearch(['date_type'])->placeholder('节假日');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->panel();
                    //$filter->expand();
                    $filter->equal('date_type')->select(\App\Services\RoomSkuTiaojiaService::Batch_tiaojia_type_arr)->width(3);

                });
            });

            return $grid;

    }

    // 批量调价取消
    public function batchTiaojiaCancel(Request $request) {
        $validator = Validator::make($request->all(), [
            'tiaojia_logid' => 'required',
        ], [
            'tiaojia_logid.required' => '调价日志ID 不能为空',
        ]);
        if ($validator->fails()) {
            return JsonResponse::make()->error($validator->errors()->first());
        }
        $tiaojia_logid = $request->get('tiaojia_logid');

        $status = RoomTiaojiaService::removeTiaojia($tiaojia_logid);
        return JsonResponse::make()->success('取消成功')->refresh();
    }

    // 保存调价日志
    public function skuBatchTiaojiaSave(Request $request) {
        $seller    = Admin::user();
        $validator = Validator::make($request->all(), [
            'act_type'           => 'required',
            'batch_tiaojia_type' => 'required|nullable',
            //'startDate'          => 'required_if:batch_tiaojia_type,0|date',
            //'endDate'            => 'required_if:batch_tiaojia_type,0|date',
            'room_sku_ids'       => 'required',
            'set_price'          => 'required',
            'set_value1'         => 'required_if:set_price,1',
            'set_value2'         => 'required_if:set_price,2',
            'set_value3'         => 'required_if:set_price,3',
            'set_value4'         => 'required_if:set_price,4',

        ], [
            'act_type.required'           => '操作项 不能为空',
            'batch_tiaojia_type.required' => '请选择一个 节假日',
            'startDate.required_if'       => '开始日期 不能空',
            'endDate.required_if'         => '结束日期 不能空',
            'startDate.date'              => '开始日期 格式不正确',
            'endDate.date'                => '结束日期 格式不正确',
            'room_sku_ids.required'       => '请选择房型销售SKU 不能空',
            'set_price.required'          => '请选择调价类型 不能空',
            'set_value1.required_if'      => '请填写调价数值 不能空',
            'set_value2.required_if'      => '请填写调价数值 不能空',
            'set_value3.required_if'      => '请填写调价数值 不能空',
            'set_value4.required_if'      => '请填写调价数值 不能空',

        ]);
        if ($validator->fails()) {
            return JsonResponse::make()->error($validator->errors()->first());
        }
        if (empty($request->batch_tiaojia_type)) {

            if (!empty($request->startDate) && !empty($request->endDate)) {

            } else {
                return JsonResponse::make()->error('请选择日期范围');
            }
        }

        if (count(array_filter($request->room_sku_ids)) <= 0) {
            return JsonResponse::make()->error('请选择房型销售SKU');
        }

        $room_sku_ids = is_array($request->room_sku_ids) ? json_encode(array_filter($request->room_sku_ids), JSON_UNESCAPED_UNICODE) : $request->room_sku_ids;

        $info = RoomSkuPrice::whereIn('id',json_decode($room_sku_ids,true))->select('room_id')->pluck('room_id');
        $room_ids = json_encode(array_flip(array_flip($info->toArray())));

        //$room_ids     = is_array($request->room_ids) ? json_encode(array_filter($request->room_ids), JSON_UNESCAPED_UNICODE) : $request->room_ids;

        $set_price    = $request->set_price;
        $insdata      = [
            'seller_id'    => $seller->id,
            'hotel_id'     => $seller->hotel_id,
            'room_ids'     => $room_ids,
            'room_sku_ids' => $room_sku_ids,
            'date_type'    => $request->batch_tiaojia_type,
            'start_date'   => $request->startDate,
            'end_date'     => $request->endDate,
            'set_price'    => $request->set_price,
            'set_value'    => $request->get('set_value' . $set_price),
        ];
        $model        = RoomTiaojiaLog::addlog($insdata);
        $mk           = (new \App\Services\RoomSkuTiaojiaService())->exeTiaojia($model->id); // 执行调价
        return JsonResponse::make()->data($request->all())->success('成功！')->refresh();
    }

}
