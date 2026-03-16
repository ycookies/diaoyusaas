<?php

namespace App\Merchant\Actions\Form;

use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use App\Models\Hotel\WxCardCodePre;
use Illuminate\Support\Facades\Cache;


class WxCardCodeDeposit extends Form implements LazyRenderable {
    use LazyWidget;
    protected $payload = [];


    public function handle(array $input) {
        $card_id = $this->payload['card_id']; //$request->get('card_id');
        $codes_rule = !empty($input['codes_rule']) ? $input['codes_rule']:'';
        $codes_num = !empty($input['codes_num']) ? $input['codes_num']:'';

        /*if(empty($codes_rule)){
            return JsonResponse::make()->error('请填写 会员卡号规则');
        }
        if(empty($codes_num)){
            return JsonResponse::make()->error('请填写 生成会员卡号数量');
        }*/
        $seller     = Admin::user();
        $wxOpen     = app('wechat.open');
        $hotel_id = $this->payload['hotel_id'];////$request->get('hotel_id');
        $gzhobj     = $wxOpen->hotelWxgzh($hotel_id);
        $codes = $this->makeCodes($codes_rule,$codes_num,$hotel_id);
        if($codes === false){
            return JsonResponse::make()->error('创建过快,创建后12分钟后在使用');
        }
        $codes_chunk = array_chunk($codes, 100); // 1000个分块
        foreach ($codes_chunk as $codeslists){
            $res = $gzhobj->card->code->deposit($card_id, $codeslists);
            addlogs('code_deposit',[$input,$this->payload],$res,0);
        }

        if(isset($res['errcode']) && $res['errcode'] == 0){
            $data = [
                'hotel_id' => $hotel_id,
                'card_id' => $card_id,
                'codes_list' => json_encode($codes),
                'codes_num' => count($codes),
            ];
            WxCardCodePre::addlog($data);

            $res1 = $gzhobj->card->code->check($card_id, $codes);
            addlogs('code_check',[$card_id],$res1,0);
            $res2 = $gzhobj->card->increaseStock($card_id, $data['codes_num']); // 增加库存
            addlogs('code_increaseStock',[$card_id],$res2,0);

            return JsonResponse::make()->data([$res])->success('导入成功')->refresh();
        }
        $error_msg = !empty($res['errmsg']) ? $res['errmsg']:'-';
        return JsonResponse::make()->data($res)->error('删除失败:'.$error_msg);
    }

    /**
     * @desc 按规则生成会员码
     * @param $codes_rule  规则
     * @param $codes_num 个数
     * author eRic
     * dateTime 2024-04-29 17:00
     */
    public function makeCodes($codes_rule,$codes_num,$hotel_id){

        /*$status = Cache::get('makeCodes');
        if(!empty($status)){
            return false;
        }*/
        $m1 = substr(date('Y'), 2) . date('md');
        $m2 = time();
        $m3 = $hotel_id;
        $m2jia = $m2 + 199;
        $lists = range($m2,$m2jia);
        foreach ($lists as $key => $numzi){
            $new_list[] = $m1.$numzi.$m3.rand(1,9);
        }
        Cache::put('makeCodes', $m2, 1100);
        return $new_list;

        $start_su = 11111111111;
        $list = range($start_su,($start_su+$codes_num));
        $new_list = [];
        foreach ($list as $key => $numzi){
            $new_list[] = $codes_rule.$numzi;
        }
    }

    public function default() {

        $data = [];
        return $data;
    }

    public function form() {

        $this->hidden('hotel_id')->value($this->payload['hotel_id']);
        $this->hidden('card_id')->value($this->payload['card_id']);
        $this->html('<h3>会员卡号编排规则说明:</h3><br> 当前年份日期+时间戳+酒店ID+1位随机数 <br> 共20位纯数字 <br><br> 每次提交随机1000个卡号 <br>');
        //$this->text('codes_rule','规则')->value(20)->help('会员卡号已这个开头，最多两位数')->rules('max:2');
        //$this->number('codes_num','个数')->value(99)->help('默认先1000个开卡数量,用完后，自动按规则加');
        //$this->textarea('codes','会员码 串')->help('逗号分隔 一次最多一百个');
        //return JsonResponse::make()->data($cardInfo)->error('删除失败:'.$error_msg);
        /**
         * "errcode": 0,
        "errmsg": "ok",
        "ticket": "gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA",
        "expire_seconds": 1800,
        "url": "http://weixin.qq.com/q/02y6zwBe6mdoG1f0qFhC4O",
        "show_qrcode_url": "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeTZ6d0JlNm1kb0cxZjBxRmhDNE8AAgS40ylmAwQIBwAA"
        }
         */

        //$this->confirm('确认已经填写完整了吗？');
        //$this->action('hotelsettings/save');
        //$form1->html('<h3>综合项</h3>');
        //$this->html('<h3>开发者可调用该接口生成一张卡券二维码供用户扫码后添加卡券到卡包</h3>');

        //$this->display('hotel_name','酒店名')->value($this->payload['name']);
        $this->disableResetButton();
        //$this->disableSubmitButton();
    }
}
