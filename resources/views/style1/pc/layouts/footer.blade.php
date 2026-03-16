<footer class="bg-main">
    <div class="container-layout">
        <ul>
            <li class="float-left item-1 hidden-l hidden-s">
                <h3 class="text-middle margin-big-bottom">关于我们</h3>
                <ul>
                    <li><a href="#" title="公司简介">公司简介</a></li>
                    <li><a href="#" title="公司荣誉">公司荣誉</a></li>
                    <li><a href="#" title="联系我们">联系我们</a></li>
                </ul>
            </li>
            <li class="float-left item-2 hidden-l hidden-s">
                <h3 class="text-middle margin-big-bottom">新闻动态</h3>
                <ul>
                    <li><a href="#" title="公司动态">公司动态</a></li>
                    <li><a href="#" title="行业资讯">行业资讯</a></li>
                    <li><a href="#" title="媒体报道">媒体报道</a></li>
                </ul>
            </li>
            <li class="float-left item-3 hidden-l hidden-s">
                <h3 class="text-middle margin-big-bottom">产品展示</h3>
                <ul>
                    <li><a href="#" title="多商户">多商户</a></li>
                    <li><a href="#" title="单商户">单商户</a></li>
                    <li><a href="#" title="城市版">城市版</a></li>
                </ul>
            </li>
            <li class="float-left item-4 hidden-l hidden-s">
                <h3 class="text-middle margin-big-bottom">视频教程</h3>
                <ul>
                    <li><a href="#" title="系统操作">系统操作</a></li>
                    <li><a href="#" title="酒店营销">酒店营销</a></li>
                </ul>
            </li>

            <li class="float-left item-5">
                <h3 class="text-middle margin-big-bottom">联系我们</h3>
                <div class="contact">
                    <div class="media media-x margin-big-bottom">
						<span class="float-left radius-circle bg-yellow text-white text-center">
							<i class="fa fa-map-marker fa-fw" aria-hidden="true"></i>
						</span>
                        <div class="media-body">
                            <p>深圳市融宝科技有限公司</p><p>
                            </p></div>
                    </div>

                    <div class="media media-x margin-big-bottom">
						<span class="float-left radius-circle bg-yellow text-white text-center">
							<i class="fa-fw fa fa-phone"></i>
						</span>
                        <div class="media-body">
                            <p>
                                <a href="tel:17681849188" class="Conv_DINCondensedC text-large">137-2558-9225</a>
                            </p><p>
                            </p></div>
                    </div>
                    <div class="media media-x margin-big-bottom">
						<span class="float-left radius-circle bg-yellow text-white text-center">
							<i class="fa fa-envelope fa-fw"></i>
						</span>
                        <div class="media-body">
                            <p>
                                <a href="mailto:demo@admin.com" class="text-middle">645657982@qq.com </a>
                            </p>
                        </div>
                    </div>

                </div>
            </li>
            <li class="float-left item-6">
                <div class="qr padding radius text-center"><img src="{{env('APP_URL')}}/images/rongbaogzh_qrcode.jpg"><p class="text-gray height-middle">关注我们</p></div>
            </li>
        </ul>
    </div>
    <div class="blank-middle"></div>
    <!-- 友情链接 -->
    <div class="container-layout">
        <div class="line">
            <div class="tab">
                <div class="tab-head">
                    <ul class="tab-nav">
                        <li class="active"><a href="#friend-1">友情链接</a></li>
                        <!-- <li><a href="#friend-2">友情链接-图片</a> </li> -->
                    </ul>
                </div>
                <div class="tab-body">
                    <div class="tab-panel active flink" id="friend-1">
                        <a href="https://www.rongbaokeji.com/" target="_blank">融宝科技</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 友情链接 end -->

    <div class="container-layout">
        <div class="line">
            <div class="copyright height">
                <div class="x8">
                    Copyright © {{$web_base['web_copyright'] ?? ''}} 版权所有　<a href="https://beian.miit.gov.cn/" rel="nofollow" target="_blank">{{$web_base['web_recordnum'] ?? ''}}</a>				</div>
                <div class="x4 text-right">
                    <a href="#">SiteMap</a>
                </div>
            </div>
        </div>
    </div>
</footer>