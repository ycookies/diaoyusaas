<?php
namespace App\Merchant\Controllers\Tuangou;


use App\Models\Hotel\HotelSetting;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Card;
// 列表
class TuangouSettingController extends AdminController
{
    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('团购设置')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->form());
    }

    protected function form()
    {
        $flds     = ['is_tuangou_refund_verify'];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        //$form = Form::make(new HotelSetting());
        //$form->setResource()
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('tuangou');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->radio('is_tuangou_refund_verify','退款审核')
            ->options([0 => '不审核',1=> '审核'])->required();

        $form->disableResetButton();
        $card =  Card::make('配置',$form);
        return $card;
    }
}
