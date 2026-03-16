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

use App\Models\Hotel\UserCenterNav;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use App\Merchant\Renderable\MinappPagesTable;
use App\Models\Hotel\MiniprogramPage;
// 列表
class UserCenterNavController extends AdminController {

    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('用户中心菜单列表')
            ->description('全部')
            ->breadcrumb(['text' => '列表', 'uri' => ''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        return Grid::make(new UserCenterNav(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('order', 'ASC');
            $grid->number();
            $grid->column('icon_img')->image('', '24', '24');
            $grid->column('title');
            $grid->column('order')->editable()->help('数值越小越靠前');
            $grid->column('path');
            $grid->column('is_show')->switch();
            $grid->column('created_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id')->width(2);
                $filter->equal('title')->width(3);

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
        return Show::make($id, new UserCenterNav(), function (Show $show) {
            $show->field('id');
            //$show->field('hotel_id');
            //$show->field('parent_id');
            $show->field('order');
            $show->field('title');
            $show->field('icon_img')->image('', '24', '24');
            $show->field('path');
            $show->field('is_show')->bool();
            $show->field('created_at');
            //$show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        return Form::make(new UserCenterNav(), function (Form $form) {
            $form->display('id');
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->hidden('parent_id')->value(0);
            $form->text('title')->help('长度2-6个汉字')->rules('required|min:2|max:6', [
                'min' => '最少2个字',
                'max' => '最大6个字',
            ])->required();
            $form->iconimg('icon_img')
                ->disk('hotel_' . Admin::user()->hotel_id)
                ->accept('jpg,png,jpeg')
                ->help('图标尺寸:64*64,格式：jpg,png,jpeg')
                ->nametype('datetime')
                ->saveFullUrl(true)
                ->remove(true)->required();
            $form->selectTable('path', '导航链接')
                ->title('选择链接')
                ->dialogWidth('80%')
                ->from(MinappPagesTable::make())->required()
                ->model(MiniprogramPage::class, 'path', 'path');

            $form->number('order');

            $form->switch('is_show');
            $form->display('created_at');
        });
    }
}
