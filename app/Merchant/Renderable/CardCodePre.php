<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Models\Hotel\WxCardCodePre;
use Dcat\Admin\Admin;
use http\Client\Request;
use Dcat\Admin\Widgets\Modal;
use App\Merchant\Actions\Form\WxCardCodeDeposit;

class CardCodePre extends LazyRenderable
{
    public function grid(): Grid
    {


        return Grid::make(WxCardCodePre::with('cardtpl'), function (Grid $grid) {

            //$req = Request();
            $grid->model()->where(['hotel_id'=> Admin::user()->hotel_id,'card_id'=>$this->payload['card_id']])->orderBy('id','DESC');
            //$grid->column('id', 'ID')->sortable();
            $grid->column('cardtpl.title','会员卡套名称');
            $grid->column('codes_num','预存卡号数量');
            $grid->column('status','核查状态');
            $grid->column('created_at');
            //$grid->column('updated_at');

            /*$modal = Modal::make()
                ->lg()
                ->title('提交小程序隐私信息')
                ->body($form)
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-arrow-up"></i> 提交小程序隐私信息</button>');
            */
            /*$modal2 = Modal::make()
                ->lg()
                ->title('导入code接口')
                ->body(WxCardCodeDeposit::make()->payload(['hotel_id'=>Admin::user()->hotel_id,'card_id'=> $this->payload['card_id']]))
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-arrow-up"></i> 导入会员code</button> &nbsp;&nbsp;');
            $grid->tools($modal2);*/

            $grid->quickSearch(['card_id'])->placeholder('会员卡套ID');
            $grid->paginate(10);
            $grid->disableActions();
            $grid->disableRowSelector();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('card_id')->width(4);
            });
        });
    }
}
