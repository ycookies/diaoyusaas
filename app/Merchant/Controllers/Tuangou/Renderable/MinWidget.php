<?php

namespace App\Merchant\Controllers\Tuangou\Renderable;

use Dcat\Admin\Widgets\Modal;

class MinWidget
{
    // 自定义分享标题
    public static function viewDiyTitleDemoImg(){
        $modal = Modal::make();
        $modal->title('查看自定义分享标题图例');
        $modal->body('<div class="text-center"><img src="'.asset('images/app-share-name.png').'" width="60%"/></div>');
        $modal->button('查看图例');
        return $modal->render();
    }
    // 自定义分享图片
    public static function viewDiyShareDemoImg(){
        $modal = Modal::make();
        $modal->title('查看自定义分享图片图例');
        $modal->body('<div class="text-center"><img src="'.asset('images/app-share-pic.png').'" width="60%"/></div>');
        $modal->button('查看图例');
        return $modal->render();
    }
}
