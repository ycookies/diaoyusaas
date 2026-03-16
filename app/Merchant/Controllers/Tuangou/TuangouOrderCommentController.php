<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers\Tuangou;

use App\Models\Hotel\Order\OrderComment;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Card;

// 订单评论列表
class TuangouOrderCommentController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('团购评价列表')
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
        return Grid::make(OrderComment::with('order','user'), function (Grid $grid) {
            $grid->model()->where(['sign'=> 'tuangou','hotel_id'=> Admin::user()->hotel_id])->orderBy('id','DESC');
            $grid->number();
            $grid->column('order.order_no','订单号')->display(function (){
                return '<a href="'.admin_url('tuangou/order?order_no='.$this->order->order_no).'"> '.$this->order->order_no.' </a>';
            });
            $grid->column('content','评论内容')->width('350')->display(function (){
                $htmls = '<p class="ml-1">'.$this->score_icon.'</p>';
                $htmls .= '<p class="ml-1">'.$this->content.'</p>';
                if(!empty($this->pic_url)){
                    $pic_url = explode(',',$this->pic_url);
                    foreach ($pic_url as  $img) {
                        $htmls .= '<img src="'.$img.'" width="80" data-action="preview-img" class="img img-thumbnail border rounded ml-1">';
                    }
                }
                $card = Card::make('<img src="'.$this->user->avatar.'" width="32" class="border rounded-circle">'.$this->user->name,$htmls)->withHeaderBorder();
                return $card->render();
            });
            //$grid->column('pic_url','图片');
            $grid->column('is_top','置顶')->switch();
            $grid->column('is_show','是否展示')->switch();
            $grid->column('created_at');
            $grid->disableCreateButton();
            //$grid->column('updated_at')->sortable();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('user_id');
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
        return Show::make($id, new OrderComment(), function (Show $show) {
            $show->field('id');
            $show->field('id');
            $show->field('hotel_id');
            $show->field('order_id');
            $show->field('order_detail_id');
            $show->field('user_id');
            $show->field('score');
            $show->field('content');
            $show->field('pic_url');
            $show->field('is_show');
            $show->field('is_virtual');
            $show->field('virtual_user');
            $show->field('virtual_avatar');
            $show->field('virtual_time');
            $show->field('goods_id');
            $show->field('goods_warehouse_id');
            $show->field('sign');
            $show->field('reply_content');
            $show->field('is_delete');
            $show->field('is_anonymous');
            $show->field('is_top');
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
        return Form::make(new OrderComment(), function (Form $form) {
            $form->display('id');
            $form->text('hotel_id');
            $form->text('order_id');
            $form->text('order_detail_id');
            $form->text('user_id');
            $form->text('score');
            $form->text('content');
            $form->text('pic_url');
            $form->text('is_show');
            $form->text('is_virtual');
            $form->text('virtual_user');
            $form->text('virtual_avatar');
            $form->text('virtual_time');
            $form->text('goods_id');
            $form->text('goods_warehouse_id');
            $form->text('sign');
            $form->text('reply_content');
            $form->text('is_delete');
            $form->text('is_anonymous');
            $form->text('is_top');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
