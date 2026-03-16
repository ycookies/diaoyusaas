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

use App\Models\Apilog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;

// 列表
class ApilogController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('接口日志管理')
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
        return Grid::make(new Apilog(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('user_id');
            $grid->column('apiname','接口名/页面')->width('160px')->display(function (){
                return $this->apiname.'<br/>'.$this->pageurl;
            });
            //$grid->column('pageurl','页面');
            $grid->column('request','请求')->display(function (){
                $request_str = '';
                try {
                    $codes = json_decode($this->request,true);
                    $request_str = json_encode($codes,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } catch (\Error $error) {

                } catch (\Exception $exception) {

                }
                return '<pre class="dump" style="max-width: 500px">'.$request_str.'</pre>';
            });
            $grid->column('result','响应')->display(function (){
                $result_str = '';
                try {
                    $codes = json_decode($this->result,true);
                    $result_str = json_encode($codes,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } catch (\Error $error) {

                } catch (\Exception $exception) {

                }
                return '<pre class="dump" style="max-width: 500px">'.$result_str.'</pre>';
            });
            $grid->column('created_at');

            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('apiname','接口名')->width(3);
        
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
        return Show::make($id, new Apilog(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('apiname');
            $show->field('pageurl');
            $show->field('request');
            $show->field('result');
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
        return Form::make(new Apilog(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('apiname');
            $form->text('pageurl');
            $form->text('request');
            $form->text('result');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
