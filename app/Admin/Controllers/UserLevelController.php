<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\UserLevel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
// 列表
class UserLevelController extends AdminController
{
    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('用户/会员等级 管理')
            ->description('列表')
            ->breadcrumb(['text' => '用户/会员等级 管理', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $req  = Request()->all();
        $type = request('_t', 1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('基础用户', url('/merchant/user-member?tab=1'));
        $tab->add('会员用户', url('/merchant/user-member?tab=2'));
        $tab->add('会员等级设置',Card::make('',$this->grid()),true);
        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new UserLevel(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('hotel_id');
            $grid->column('level_name');
            $grid->column('level_desc');
            $grid->column('level_logo');
            $grid->column('min_booking_num');
            $grid->column('max_booking_num');
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
        return Show::make($id, new UserLevel(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_id');
            $show->field('level_name');
            $show->field('level_desc');
            $show->field('level_logo');
            $show->field('min_booking_num');
            $show->field('max_booking_num');
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
        return Form::make(new UserLevel(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('level_name');
            $form->text('level_desc');
            $form->text('level_logo');
            $form->text('min_booking_num');
            $form->text('max_booking_num');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
