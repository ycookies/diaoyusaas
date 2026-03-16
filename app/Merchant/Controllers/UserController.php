<?php

namespace App\Merchant\Controllers;

use App\Merchant\Repositories\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class UserController extends AdminController
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
        return Grid::make(new User(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('join_time');
            $grid->column('img');
            $grid->column('openid');
            $grid->column('uniacid');
            $grid->column('tel');
            $grid->column('type');
            $grid->column('level_id');
            $grid->column('score');
            $grid->column('zs_name');
            $grid->column('number');
            $grid->column('commission');
            $grid->column('balance');
            $grid->column('ali_user_id');
            $grid->column('avatar');
            $grid->column('province');
            $grid->column('city');
            $grid->column('nick_name');
            $grid->column('is_student_certified');
            $grid->column('user_status');
            $grid->column('user_type');
            $grid->column('is_certified');
            $grid->column('gender');
            $grid->column('access_token');
            $grid->column('expires_in');
            $grid->column('refresh_token');
            $grid->column('re_expires_in');
            $grid->column('create_time');
            $grid->column('update_time');
            $grid->column('cert_no');
            $grid->column('cert_type');
            $grid->column('user_name');
            $grid->column('session_key');
            $grid->column('token');
        
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
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('join_time');
            $show->field('img');
            $show->field('openid');
            $show->field('uniacid');
            $show->field('tel');
            $show->field('type');
            $show->field('level_id');
            $show->field('score');
            $show->field('zs_name');
            $show->field('number');
            $show->field('commission');
            $show->field('balance');
            $show->field('ali_user_id');
            $show->field('avatar');
            $show->field('province');
            $show->field('city');
            $show->field('nick_name');
            $show->field('is_student_certified');
            $show->field('user_status');
            $show->field('user_type');
            $show->field('is_certified');
            $show->field('gender');
            $show->field('access_token');
            $show->field('expires_in');
            $show->field('refresh_token');
            $show->field('re_expires_in');
            $show->field('create_time');
            $show->field('update_time');
            $show->field('cert_no');
            $show->field('cert_type');
            $show->field('user_name');
            $show->field('session_key');
            $show->field('token');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new User(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('join_time');
            $form->text('img');
            $form->text('openid');
            $form->text('uniacid');
            $form->text('tel');
            $form->text('type');
            $form->text('level_id');
            $form->text('score');
            $form->text('zs_name');
            $form->text('number');
            $form->text('commission');
            $form->text('balance');
            $form->text('ali_user_id');
            $form->text('avatar');
            $form->text('province');
            $form->text('city');
            $form->text('nick_name');
            $form->text('is_student_certified');
            $form->text('user_status');
            $form->text('user_type');
            $form->text('is_certified');
            $form->text('gender');
            $form->text('access_token');
            $form->text('expires_in');
            $form->text('refresh_token');
            $form->text('re_expires_in');
            $form->text('create_time');
            $form->text('update_time');
            $form->text('cert_no');
            $form->text('cert_type');
            $form->text('user_name');
            $form->text('session_key');
            $form->text('token');
        });
    }
}
