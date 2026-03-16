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

use App\Models\Hotel\Huodong;
use App\Models\Hotel\HuodongUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Support\Str;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use App\Merchant\Renderable\HuodongBaomingTable;
use Dcat\Admin\Widgets\Alert;

// 列表
class HuodongController extends AdminController
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

        $grid = Grid::make(new Huodong(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('act_code','活动ID');
            $grid->column('is_online','活动场景')->using(['1'=>'线上','2'=>'线下']);
            $grid->column('act_name');
            //$grid->column('act_type');
            $grid->column('act_banner')->image('','100');
            // $grid->column('act_haibao');
            // $grid->column('begin_time');
            // $grid->column('end_time');
            // $grid->column('act_didian');
            $grid->column('act_rensu')->display(function ($act_rensu) {
                if($act_rensu == 0){
                    return '不限人数';
                }
                return $act_rensu . '人';
            });
            $grid->column('act_cost')->display(function ($act_cost) {
                if($act_cost == 0){
                    return '免费';
                }
                return '¥'.$act_cost;
            });
            // $grid->column('act_content');
            // $grid->column('hdurl');
            $grid->column('short_url','活动专码')->image('','100','100');
            // $grid->column('organizer');
            // $grid->column('join_num');
            // $grid->column('share_num');
            $grid->column('is_hot')->switch();
            $grid->column('is_active','启用')->switch();
            $grid->column('baoming_num','已报名')->display(function(){
                return HuodongUser::where(['hd_id' => $this->id])->count();
            })->modal(function ($modal) {
                // 设置弹窗标题
                $modal->title('活动报名列表');
                // 自定义图标
                $modal->icon('feather icon-chevrons-right');
                $card = new Card(null, HuodongBaomingTable::make()->payload(['id' => $this->id]));
                return "<div style='padding:10px 10px 0'>$card</div>";
            });
            $grid->column('created_at');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                //$actions->disableDelete();
                // 去掉编辑
                //$actions->disableEdit();
                $actions->disableView();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('act_name');
        
            });
        });
        $htmll = <<<HTML
<ol>
    <li>默认四个菜单图标一栏,请注意!</li>
</ol>
HTML;
        $alert = Alert::make($htmll, '提示:')->info();
        return $grid;
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
        return Show::make($id, new Huodong(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('is_online');
            $show->field('act_code');
            $show->field('act_name');
            $show->field('act_type');
            $show->field('act_banner');
            $show->field('act_haibao');
            $show->field('begin_time');
            $show->field('end_time');
            $show->field('act_didian');
            $show->field('act_rensu');
            $show->field('act_cost');
            $show->field('act_content');
            $show->field('hdurl');
            $show->field('short_url');
            $show->field('organizer');
            $show->field('join_num');
            $show->field('share_num');
            $show->field('is_hot');
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
        return Form::make(new Huodong(), function (Form $form) {
            //$form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->radio('is_online','活动场景')
            ->when('1',function (Form $form){
            })
            ->when('2',function (Form $form){
                $form->text('act_didian');
            })
            ->options(['1'=>'线上','2'=>'线下'])->default(1)->required();
            
            $form->select('act_type')
            ->options(['1'=>'团购','2'=>'新品发布','3'=>'品鉴活动'])->required();
            $form->text('act_code')->value(Str::random(10))->help('最多10个字符')->required();
            $form->text('act_name')->help('最多16个字符')->required();
            
            $form->photo('act_banner')->help('尺寸:750*500px, jpg,png,jpeg')
            ->disk('hotel_'.Admin::user()->hotel_id)
            ->accept('jpg,png,jpeg')
            ->nametype('datetime')
            ->saveFullUrl(true)
            ->remove(true)
            ->required();
            //$form->photo('act_haibao');
            $form->datetimeRange('begin_time','end_time','报名时间段')->required();
            //$form->text('end_time');
            
            $form->number('act_rensu')->help('填0表示不限制人数')->required();
            $form->currency('act_cost','报名参加费用')->help('单位:元 填0表示免费')->required();
            $form->switch('is_hot','是否热门');
            $form->switch('is_active','是否激活');
            $form->editor('act_content')->required();
            $form->submitted(function (Form $form) {
                // 获取用户提交参数
                if($form->isCreating()){
                    $act_code = $form->act_code;
                    $full_filename = $form->hotel_id.'-huodong-'.$act_code.'-qrcode.png';
                    $minapp_qrcode = app('wechat.open')->getMinappQrcode('',$form->hotel_id,'/pages2/huodong/detail?act_code='.$act_code,$full_filename,1);
                    $form->short_url = $minapp_qrcode;
                }
            });
            // $form->text('hdurl');
            $form->hidden('short_url');
            // $form->text('organizer');
            // $form->text('join_num');
            // $form->text('share_num');
        });
    }
}
