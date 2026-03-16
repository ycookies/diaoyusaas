<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Models\Hotel\RoomSkuPrice;

class MinappRoomSkuQrcode extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        return $this->success('保存成功');
    }

    public function default()
    {

        //$full_filename = str_replace('/','-',$this->payload['path']).'-qrcode.png';

        return [
            // 展示上个页面传递过来的值
            'hotel_id' => $this->payload['hotel_id'] ?? '',
            'path' => $this->payload['path'] ?? '',
            'name' => $this->payload['name'] ?? '',
        ];
    }

    public function form()
    {
        $qrcode_img = '';
        if(!isset($this->payload['roomsku_qrcode'])){
            $full_filename = str_replace('/','-',$this->payload['hotel_id'].'-'.md5($this->payload['path'])).'-qrcode.png';

            $minapp_qrcode = app('wechat.open')->getMinappQrcode('',$this->payload['hotel_id'],'/'.$this->payload['path'],$full_filename,1);
            /*$scene = $this->payload['scene'];
            $optional = ['page'=> $this->payload['path']];
            $minapp_qrcode = app('wechat.open')->getUnlimitedQRCode('',$this->payload['hotel_id'],$scene,$optional,$full_filename,1);
            */
            if($minapp_qrcode !== false){
                $key = $this->payload['key'];
                RoomSkuPrice::upQrcode($key,$minapp_qrcode);
                $qrcode_img = $minapp_qrcode;
            }
        }else{
            $qrcode_img = $this->payload['roomsku_qrcode'];
        }



        $this->text('name','页面名称')->readOnly();
        $this->text('path','路径')->readOnly();
        if($qrcode_img == ''){
            $this->html('<h3>获取小程序码出错</h3>')->label('商品小程序码');

        }else{
            $this->html('<div style="margin-top: 0px;"><img width="140" src="'.$qrcode_img.'" /></div><a onclick="downimg(\''.$qrcode_img.'\')" href="javascript:void(0);">点击下载</a>')
                ->label('商品小程序码');

            //$this->html('<a id="saveLink" href="'.$qrcode_img.'">点击下载</a>')->label('下载图片');
        }
        $this->disableSubmitButton();
        $this->disableResetButton();
    }
}
