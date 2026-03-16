<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Form\ExampleImg;
use App\Admin\Extensions\Form\MultipleExampleImg;
use App\Models\Hotel\JinjianDoc;
use App\Models\Hotel\RuzhuInfo;
use App\Models\Hotel\Seller as SellerModels;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/*use SuperEggs\DcatDistpicker\Filter\DistpickerFilter;
use SuperEggs\DcatDistpicker\Form\Distpicker;
use SuperEggs\DcatDistpicker\Grid\Distpicker as GridDistpicker;*/
//use App\Models\Hotel\Hotel;
// 酒店入驻材料收集
class RuzhuController extends Controller {
    public $doc_info; // 入驻材料收集

    // 创建商户进件链接
    public function createLink(Content $content) {
        Admin::css('/css/material-collection.css');
        Admin::script(
            <<<JS
(function () {
   var clipboard = new ClipboardJS('.clipboard-txt');
   clipboard.on('success', function(e) {
       e.clearSelection();
       layer.msg('已复制');
    });
    clipboard.on('error', function(e) {
        e.clearSelection();
        layer.msg('复制内容失败');
    });
})()    
JS
        );
        admin_inject_section(Admin::SECTION['APP_INNER_BEFORE'], function () {
            $html = <<<HTML
<div class="header" style="height: 100px;margin-bottom: 20px;">
        <div class="wx-wrap">
            <div class="logo">
                <h1 class="main-logo"><a href="#">融宝易住</a></h1>
                <div class="sub-logo"></div>
            </div>
        </div>
    </div>
HTML;
            return $html;
        });


        $row = function (Row $row) {
            $form = Form::make(new JinjianDoc());
            $form->confirm('确认现在生成吗？');
            $form->action('/material-collect/create-link/save');
            $form->block(12, function (Form\BlockForm $form) {

                $form->display('hotel_bd', '业务BD')->value('钟总');

                $form->next(function (Form\BlockForm $form) {
                    $business_code = '1566291601_' . time();
                    $form->title('创建商户进件链接<br/><span style="font-size: 12px;font-weight: 400;color:#737373">每个链接唯一使用</span>');
                    $form->text('hotel_name', '酒店名称')->required();
                    $form->hidden('doc_link', '酒店名称')->value(url('admin/material-collect/form?business_code=' . $business_code))->required();
                    $form->hidden('business_code', '酒店名称')->value($business_code)->required();
                    $form->hidden('hotel_bd', '业务BD')->value('钟总');
                    //$form->button('保存并下一步');
                    $form->submitButton('创建进件链接');
                });
                $form->next(function (Form\BlockForm $form) {
                    $form->title('商户进件链接管理<br/><span style="font-size: 12px;font-weight: 400;color:#737373">所有历史进件链接</span>');
                    $grid = Grid::make(new JinjianDoc(), function (Grid $grid) {
                        $grid->model()->orderBy('id', 'DESC');
                        $grid->column('id');
                        $grid->column('hotel_name', '酒店名');
                        $grid->column('doc_link', '资料进件链接')->display(function ($doc_link) {
                            return "<a target='_blank' href='" . $doc_link . "'>查看</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class='clipboard-txt' data-clipboard-text='" . $doc_link . "'> <i class='feather icon-copy'></i></span>";
                        });
                        $grid->actions(function ($actions) {
                            $id = $actions->getKey();
                            // 去掉删除
                            $actions->disableDelete();
                            // 去掉编辑
                            $actions->disableEdit();
                        });
                        $grid->disableActions();
                        $grid->disableBatchActions();
                        $grid->disableDeleteButton();
                        $grid->disableBatchDelete();
                        $grid->disableCreateButton();
                        $grid->setActionClass(Grid\Displayers\Actions::class);
                    });
                    $form->html($grid);
                });
            });
            $row->column(1, '');
            $row->column(10, $form);
            $row->column(1, '');
        };

        return $content->full()->header('商户进件')->description('支付商户进件')->row($row);
    }

    //
    public function createLinkSave(Request $request) {
        $request->validate(
            [
                'hotel_name' => 'required',
                /*'hotel_db' => 'required',
                'doc_link' => 'required',
                'business_code' => 'required',*/
            ], [
                'hotel_name.required' => '请填写酒店名称 不能为空',
                //'phone_code.required'  => '手机验证码 不能为空',
            ]
        );
        $insdata = [
            'hotel_bd'      => $request->hotel_bd,
            'hotel_name'    => $request->hotel_name,
            'doc_link'      => $request->doc_link,
            'business_code' => $request->business_code,
            'status'        => 0,
        ];
        $model   = JinjianDoc::create($insdata);
        if (!empty($model->id)) {
            return (new WidgetForm())->response()->success('创建成功')->refresh();
        }
        return (new WidgetForm())->response()->error('创建失败');
    }


    // 进件表单页
    public function index(Content $content) {
        Form::extend('exampleimg', ExampleImg::class);
        Form::extend('multipleexampleimg', MultipleExampleImg::class);
        Admin::css('/css/material-collection.css');
        admin_inject_section(Admin::SECTION['APP_INNER_BEFORE'], function () {
            $html = <<<HTML
<div class="header" style="height: 100px;margin-bottom: 20px;">
        <div class="wx-wrap">
            <div class="logo">
                <h1 class="main-logo11">融宝易住 - 商户入驻</h1>
                <div class="sub-logo"></div>
            </div>
        </div>
    </div>
HTML;
            return $html;
        });

        $request        = Request();
        $business_code  = $request->get('business_code');
        $role           = $request->get('role');
        $jinjianDoc     = JinjianDoc::where(['business_code' => $business_code])->first();
        $this->doc_info = $jinjianDoc;

        $info = RuzhuInfo::where(['business_code' => $business_code])->first();
        /*if (!$info) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html('<h2><i style="color:red" class="feather icon-alert-circle"></i> 未找到进件申请单信息</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('欢迎你，创建微信支付商户号')->description('')->row($form2);
        }*/
        if (!empty($info->status) && $info->status == 1 && empty($role)) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html('<h2><i style="color:red" class="feather icon-check-circle"></i> 入驻资料已经提交，待审核</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('融宝易住 商户入驻')->description('')->row($form2);
        }

        if (!empty($info->status) && $info->status == 2) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html("<h4>申请对象: " . $this->doc_info->hotel_name . "</h4>");
                $form->html("<h5>业务申请编号: " . $this->doc_info->business_code . "</h5>");
                $form->html('<h2><i style="color:green" class="feather icon-check-circle"></i> 商户入驻申请单已完成</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('融宝易住 商户入驻')->description('')->row($form2);
        }

        //$form = Form::make(new RuzhuInfo())->edit($this->doc_info->id);
        if(!empty($info->status)){
            $form = Form::make(new RuzhuInfo())->edit($info->id);
        }else{
            $form = Form::make(new RuzhuInfo());
        }

        $form->confirm('确认已经填写完整了吗？');
        $form->action('/ruzhu/form/save');
        $form->block(1, function (Form\BlockForm $form) {
        });
        $form->block(10, function (Form\BlockForm $form) {
            //$form->html($html);
            //$form->html("<h1>欢迎前来入驻 融宝易住</h1>");
            $form->html("<h4>申请入驻对象: " . $this->doc_info->hotel_name . "</h4>");
            $form->html("<h5>业务申请编号: " . $this->doc_info->business_code . "</h5>");
            $role = Request()->get('role');
            if (!empty($role) && $role == 1 && $this->doc_info->status == 1) {
                $form->html('<h2><i style="color:red" class="feather icon-check-circle"></i> 入驻资料已经提交，待审核</h2>');
            }
            $form->next(function (Form\BlockForm $form) {
                $form->hidden('business_code')->value($this->doc_info->business_code);
                $form->text('name', '酒店名称')->readOnly()->value($this->doc_info->hotel_name)->required();
                $form->select('store_type', '酒店类型')->width(2)->options(SellerModels::$store_type_arr)->required();
                $form->select('star', '星级')->width(2)->options(SellerModels::$hotel_star_arr)->required();
                $form->text('store_brand', '酒店品牌');
                //$form->hidden('hotel_user_id')->value($seller->id);
                $form->date('open_time', '开业时间')->required();
                $form->date('decorate_time', '装修时间')->required();
                $form->number('room_num', '房间总数')->required();
                $form->text('tel', '酒店电话')->required();
                //$form->text('link_tel')->required();
                $form->text('link_name', '联系人')->required();
                $form->distpicker(['province_id', 'city_id', 'district_id'], '经营场所在地')->required();
                $form->text('address', '酒店所在地址')->required();
                $form->text('coordinates', '经纬度坐标')->help('<a target="_blank" href="https://lbs.qq.com/getPoint/"> >>> 去拾取位置坐际</a>')->required();
                $form->textarea('tese_info', '酒店特色');
                $form->textarea('overview', '酒店概览');
                $form->editor('introduction', '酒店图文介绍');
                $form->editor('prompt', '订房必读');
                $form->editor('brand_info', '品牌介绍');
                $form->hidden('act_type')->value('base')->required();
            });
            $form->next(function (Form\BlockForm $form) {
                $form->title('酒店Logo/场景图');
                $form->image('ewm_logo', '酒店Logo')
                    ->width(3)
                    ->help('建议尺寸大小:541*500')
                    ->url('uploads')
                    ->autoUpload()
                    ->retainable()
                    ->saveFullUrl()
                    ->required();
                $form->multipleImage('img', '酒店场景照')
                    ->url('uploads')
                    ->autoUpload()
                    ->retainable()
                    ->saveFullUrl()
                    ->required();
                $form->multipleImage('room_img', '房间精美照')
                    ->url('uploads')
                    ->autoUpload()
                    ->retainable()
                    ->saveFullUrl();

            });
            $form->next(function (Form\BlockForm $form) {
                $form->width(11,1);
                $form->title('房型信息');
                $form->table('room_list','', function ($table) {
                    $table->text('title','房型名称')->prepend('');
                    $table->currency('price','销售价(元/天)');
                    $table->multipleImage('room_type_img','房间照片')
                        ->url('uploads')
                        ->autoUpload()
                        ->retainable()
                        ->saveFullUrl()
                        ->required();
                });

                /*$form->multipleImage('room_img', '房间照片合集')
                    ->help('建议尺寸大小:220*180')
                    ->url('uploads')
                    ->autoUpload()
                    ->retainable()
                    ->saveFullUrl();*/

                $form->submitButton('提交资料-申请入驻');
            });

        });
        $form->block(1, function (Form\BlockForm $form) {

        });

        //$card1 = Card::make('', $alert->info() . $form);

        return $content->full()->header('欢迎前来入驻 融宝易住')->description('欢迎前来入驻 融宝易住')->row($form);
    }

    // 进件表单保存
    public function formSave(Request $request) {
        $validator = \Validator::make($request->all(), [
            'business_code' => 'required',
            //'hotel_name'    => 'required',
            'store_type'    => 'required',
            'star'          => 'required',
            'store_brand'   => 'required',
            'open_time'     => 'required',
            'decorate_time' => 'required',
            'room_num'      => 'required|numeric',
            'tel'           => 'required',
            'link_name'     => 'required',
            'coordinates'   => 'required',
            'district_id'   => 'required',
            'province_id'   => 'required',
            'city_id'       => 'required',
            'address'       => 'required',
            'tese_info'     => 'required',
            'overview'      => 'required',
            'introduction'  => 'required',
            'prompt'        => 'required',
            'brand_info'    => 'required',
            'ewm_logo'      => 'required',
            'img'           => 'required',
            //'room_img'      => 'required',
        ], [
            'business_code.required' => '业务编号 不能为空',
            'hotel_name.required'    => '酒店名称 不能为空',
            'store_type.required'    => '酒店类型 不能为空',
            'star.required'          => '星级 不能为空',
            'store_brand.required'   => '酒店品牌 不能为空',
            'open_time.required'     => '开业时间 不能为空',
            'decorate_time.required' => '装修时间 不能为空',
            'room_num.required'      => '房间总数 不能为空',
            'room_num.numeric'       => '房间总数 只能是数字',
            'tel.required'           => '酒店电话 不能为空',
            'link_name.required'     => '联系人 不能为空',
            'coordinates.required'   => '经纬度坐标 不能为空',
            'province_id'           => '请选择省份',
            'city_id'               => '请选择城市',
            'district_id'           => '请选择地区',
            'address.required'      => '酒店所在地址 不能为空',
            'tese_info.required'    => '酒店特色 不能为空',
            'overview.required'     => '酒店概览 不能为空',
            'introduction.required' => '酒店图文介绍 不能为空',
            'prompt.required'       => 'prompt 不能为空',
            'brand_info.required'   => '品牌介绍 不能为空',
            'ewm_logo.required'     => '酒店Logo 不能为空',
            'img.required'          => '酒店场景图片 不能为空',
            'room_img.required'     => '房间照片 不能为空',
        ]);

        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->all()[0]);
        }
        //info($request->all());
        $business_code = $request->get('business_code');
        $info          = JinjianDoc::where(['business_code' => $business_code])->first();
        if (!$info) {
            return (new WidgetForm())->response()->error('未找到进件业务单信息');
        }
        $ewm_logo = $request->ewm_logo;
        if (strpos($ewm_logo, 'http') === false) {
            $ewm_logo = env('APP_URL') . '/jinjiandoc/' . $ewm_logo;
        }

        $room_list = $request->room_list;
        $room_list_arr = [];
        if(!empty($room_list)){
            foreach ($room_list as $key => $valit) {
                $room_list_arr[] = [
                    'title' => $valit['title'],
                    'price' => $valit['price'],
                    'room_img' => $this->imgsHanle($valit['room_type_img']),
                ];
            }

        }
        $updata       = [
            'business_code' => $request->business_code,
            'hotel_name'    => $request->name,
            'store_type'    => $request->store_type,
            'star'          => $request->star,
            'store_brand'   => $request->store_brand,
            'open_time'     => $request->open_time,
            'decorate_time' => $request->decorate_time,
            'room_num'      => $request->room_num,
            'tel'           => $request->tel,
            'link_name'     => $request->link_name,
            'coordinates'   => $request->coordinates,
            'province_id'   => $request->province_id,
            'city_id'       => $request->city_id,
            'district_id'   => $request->district_id,
            'address'       => $request->address,
            'tese_info'     => $request->tese_info,
            'overview'      => $request->overview,
            'introduction'  => $request->introduction,
            'prompt'        => $request->prompt,
            'brand_info'    => $request->brand_info,
            'ewm_logo'      => $ewm_logo,
            'img'           => $this->imgsHanle($request->img),
            'room_img'      => $this->imgsHanle($request->room_img),
            'room_list' => !empty($room_list_arr) ? json_encode($room_list_arr,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES):'',
        ];
        $model = RuzhuInfo::firstOrCreate(['business_code' => $business_code], $updata);
        if ($model) {
            WxRobotDoc('[商户入驻] 资料已提交', '商户入驻资料', $business_code);
            return (new WidgetForm())->response()->success('提交成功')->refresh();
        }
        return (new WidgetForm())->response()->error('提交失败');
    }

    public function imgsHanle($imgsrc){
        if(empty($imgsrc)){
            return '';
        }
        $room_img_arr     = explode(',', $imgsrc);
        $room_img_arr_new = [];
        foreach ($room_img_arr as $infow) {
            $urljk = $infow;
            if (strpos($infow, 'http') === false) {
                $urljk = env('APP_URL') . '/jinjiandoc/' . $urljk;
            }
            $room_img_arr_new[] = $urljk;
        }
        $room_img_pic = implode(',', $room_img_arr_new);
        return $room_img_pic;
    }
}
