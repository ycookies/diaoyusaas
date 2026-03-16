<?php

namespace App\Admin\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Admin\Repositories\HotelSettingRep;
use App\Models\Hotel\HotelGoldPlan;
use App\Models\Hotel\Hotel;
class GoidplanUpverifyfile extends Form implements LazyRenderable
{
    use LazyWidget;
    protected $payload = [];

    public function handle(array $input)
    {
        $hotel_id = $this->payload['id'];
        HotelGoldPlan::where('hotel_id',$hotel_id)->update(['verify_file'=>$input['verify_file']]);
        return $this->response()->success('保存成功')->refresh();
    }
    public function form()
    {
        //$this->confirm('确认已经填写完整了吗？');
        $this->file('verify_file','验签文件')->url('uploads/verifyFile')->autoUpload();
        $this->disableResetButton();
    }
}
