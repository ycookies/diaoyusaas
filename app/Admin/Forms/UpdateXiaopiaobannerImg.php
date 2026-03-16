<?php

namespace App\Admin\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Admin\Repositories\HotelSettingRep;
use App\Models\Hotel\HotelGoldPlan;
use App\Models\Hotel\Hotel;

class UpdateXiaopiaobannerImg extends Form implements LazyRenderable
{
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input)
    {
        $hotel_id = $this->payload['id'];
        $xiaopiao_banner_img = env('APP_URL') . '/uploads/' . $input['xiaopiao_banner_img'];
        HotelGoldPlan::where('hotel_id', $hotel_id)->update(['xiaopiao_banner_img' => $xiaopiao_banner_img]);
        return $this->response()->success('保存成功')->refresh();
    }
    public function form()
    {
        //$this->confirm('确认已经填写完整了吗？');
        $this->image('xiaopiao_banner_img', '自定义商家小票banner图')
            ->url('uploads-web')
            ->default(env('APP_URL') . '/img/xiaop-banner1.jpg')
            ->saveFullUrl()
            ->uniqueName()
            ->help('尺寸：720*240. 支持png、jpg、jpeg格式')
            ->dimensions(['width' => 720, 'height' => 240])
            ->autoUpload()->required();
        $this->disableResetButton();
    }
}
