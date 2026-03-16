<?php

namespace App\Portal\Actions;

use App\Portal\Forms\AdminSetting as AdminSettingForm;
use Dcat\Admin\Actions\Action;
use Dcat\Admin\Widgets\Modal;
use Illuminate\Http\Request;
use App\Portal\Forms\CacheClearFrom;

class CacheClear extends Action
{
    /**
     * @return string
     */
	protected $title = '<i class="fa fa-trash-o" style="font-size: 1.5rem"></i> 清除缓存';

    public function render()
    {
        $modal = Modal::make()
            ->id('admin-setting-config') // 导航栏显示弹窗，必须固定ID，随机ID会在刷新后失败
            ->title('清除缓存')
            ->body(CacheClearFrom::make())
            ->button(
                <<<HTML
<ul class="nav navbar-nav">
     <li class="nav-item"> &nbsp;{$this->title()} &nbsp;</li>
</ul> 
HTML
            );

        return $modal->render();

    }
    public function handle(Request $request)
    {
        return $this->response()->success('成功！');
    }

    public function confirm()
    {
        return '你确定要清除缓存吗？';
    }
}
