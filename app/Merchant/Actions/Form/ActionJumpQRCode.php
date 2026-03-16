<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Widgets\Alert;
class ActionJumpQRCode extends Form implements LazyRenderable {
    use LazyWidget;

    protected $payload = [];
    protected $authinfo;

    public function handle(array $input) {
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $authinfo = $openPlatform->getOauthInfo('',Admin::user()->hotel_id);
        $this->authinfo = $authinfo;

        if(!empty($this->payload['prefix'])){
            // 修改 $prefix,$path
            $res = $miniProgram->setting->addJumpQRCode(1,$input['prefix'],$input['path']);
            addlogs('editJumpQRCode',$input,$res);
        }else{
            // 新增
            $down_status = $this->downCheckFile();
            if($down_status !== true){
                return $this->response()->error('下载校验文件失败');
            }
            $res = $miniProgram->setting->addJumpQRCode(0,$input['prefix'],$input['path'],1,$input['permit_sub_rule']);
            addlogs('addJumpQRCode',$input,$res);
        }

        if(isset($res['errcode']) && $res['errcode'] == 0){
            return $this->response()->success('操作成功')->refresh();
        }
        $errormsg = '操作失败';
        if(isset($res['errcode']) && $res['errcode'] == 85069){
            $errormsg = '验证 校验文件失败';
        }
        return JsonResponse::make()->error($errormsg);
    }

    // 下载校验文件
    public function downCheckFile(){
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->setting->downloadQRCodeText();
        addlogs('downloadQRCodeText',['hotel_id'=>Admin::user()->hotel_id],$res);

        $file_name = '';
        $file_content = '';
        if(isset($res['errcode']) && $res['errcode'] == 0){
            $file_name = $res['file_name'];
            $file_content = $res['file_content'];
            $errormsg = '';
            // 获取子商户号
            $sub_mch_id = $this->authinfo->sub_mch_id;
            $dir = public_path('/qr/'.$sub_mch_id);
            if(!file_exists($dir)){
                mkdir($dir,0777,true);
            }
            $path = public_path('/qr/'.$sub_mch_id.'/'.$file_name);
            if(!file_exists($path)){
                file_put_contents($path,$file_content);
                if(!file_exists($path)){
                    return '下载文件失败';
                }
            }

            return true;
        }
        return '下载文件失败';
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {
        $openPlatform = app('wechat.open');
        $authinfo = $openPlatform->getOauthInfo('',Admin::user()->hotel_id);
        $this->authinfo = $authinfo;

        if(!empty($this->payload['prefix'])){
            $this->confirm('确认已经填写完整了吗？');
            $alert = Alert::make('如何设置请参考官方文档:<a href="https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html#%E4%BA%8C%E7%BB%B4%E7%A0%81%E8%A7%84%E5%88%99" target="_blank"> 查看</a>','提示:')->info();
            $this->html($alert);
            $this->text('prefix','二维码规则')
                ->value($this->payload['prefix'])
                ->help('请填写符合要求的二维码规则 <a href="https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html#%E4%BA%8C%E7%BB%B4%E7%A0%81%E8%A7%84%E5%88%99" target="_blank"> 详细说明</a>')->required();
            $this->text('path','小程序功能页面')
                ->value($this->payload['path'])->help('请填写扫码后跳转的小程序功能页面，如：pages/index/index')->required();
            //$this->radio('permit_sub_rule','独占符合二维码前缀')->help('选择是否独占符合二维码前缀匹配规则的所有子规则')->options(['1'=> '不占用','2'=>'占用' ])->default('1');

        }else{
            $this->confirm('确认已经填写完整了吗？');
            $alert = Alert::make('如何设置请参考官方文档:<a href="https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html#%E4%BA%8C%E7%BB%B4%E7%A0%81%E8%A7%84%E5%88%99" target="_blank"> 查看</a>','提示:')->info();
            $this->html($alert);
            //$this->html('下载校验文件')->label('校验文件')->help('请下载校验文件，并根据说明文档要求将文件上传至服务器指定目录https://hotel.rongbaokeji.com/qr/WoaEgmkwJ5.txt下，并确保可以访问');
            $this->text('prefix','二维码规则')->default(env('APP_URL').'/qr/'.$this->authinfo->sub_mch_id.'/')->help('请填写符合要求的二维码规则 <a href="https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html#%E4%BA%8C%E7%BB%B4%E7%A0%81%E8%A7%84%E5%88%99" target="_blank"> 详细说明</a>')->required();
            $this->text('path','小程序功能页面')->help('请填写扫码后跳转的小程序功能页面，如：pages/index/index')->required();
            $this->radio('permit_sub_rule','独占符合二维码前缀')->help('选择是否独占符合二维码前缀匹配规则的所有子规则')->options(['1'=> '不占用','2'=>'占用' ])->default('1')->required();
        }

        $this->disableResetButton();
    }
}
