<?php

namespace App\Merchant\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Contracts\LazyRenderable;
use App\Services\KefuService;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Traits\LazyWidget;

class KefuUI extends Form implements LazyRenderable
{
    use LazyWidget;
    public function handle(array $input)
    {

        $user_openid = !empty($input['user_openid']) ? $input['user_openid']:'';
        $send_content = !empty($input['send_content']) ? $input['send_content']:'';

        $ser = (new KefuService())->sendMsg($user_openid,$send_content);
        if($ser === true){
            return $this->response()->success('回复成功')->refresh();
        }
        return $this->response()->error('回复失败,遇到问题:');
    }

    public function default()
    {

        return [];
    }

    public function form()
    {
        $user_openid = !empty($this->payload['user_openid']) ? $this->payload['user_openid']:'';
        $this->width(9,3);
        $this->hidden('user_openid')->value($user_openid);
        $this->textarea('send_content','回复内容')->required();
        $this->disableResetButton();
    }
}
