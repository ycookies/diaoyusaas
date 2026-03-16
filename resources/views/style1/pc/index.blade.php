@extends('style1.pc.layouts.top')
@section('title','首页')
@section('content')
    <link href="{{asset('style1/Lib/OwlCarousel2.21/owl.carousel.min.css') }}" rel="stylesheet">
    <!-- 轮播广告 -->
    <div class="layout bg-gray">
        <div class="line">
            <div class="x12">
                <div class="slides owl-carousel dot-center slides-arrow">
                    @foreach ($slides as $item)
                        <div class="item">
                            <a href='{{$item->links}}' @if($item->target == 1) target="_blank" @endif><img
                                        src="{{$item->litpic}}" class="img-responsive"></a>
                        </div>
                    @endforeach
                    {{--<div class="item">
                        <a href='#'><img src="{{asset('images/banner4.jpg')}}" class="img-responsive"></a>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
    <!-- 轮播广告 -->
    <style>
        section#about {
            position: relative;
            background: #f6f6f6;
        }

        section#about .about-item {
            text-align: center;
            font-size: 17px;
            line-height: 25px;
            color: #999999;
        }

        section#about .about-item i.fa {
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            -ms-border-radius: 50%;
            -o-border-radius: 50%;
            border-radius: 50%;
            -webkit-transition: all 0.25s ease-in-out;
            -moz-transition: all 0.25s ease-in-out;
            -ms-transition: all 0.25s ease-in-out;
            -o-transition: all 0.25s ease-in-out;
            transition: all 0.25s ease-in-out;
            width: 100px;
            height: 100px;
            text-align: center;
            line-height: 100px;
        }

        section#about .about-item h3 {
            color: #333;
            font-size: 18px;
            line-height: 40px;
            font-weight: bold;
        }

        section#about .about-item p {
            color: #999;
            font-size: 14px;
            line-height: 24px;
        }

        section#about .about-item i.bianse1 {
            border: 1px solid #ddd;
            color: #1f7dfc;
        }

        section#about .about-item i.bianse2 {
            border: 1px solid #ddd;
            color: #9a71e9;
        }

        section#about .about-item i.bianse3 {
            border: 1px solid #ddd;
            color: #fc9137;
        }

        section#about .about-item i.bianse4 {
            border: 1px solid #ddd;
            color: #79e3c9;
        }

        section#about .about-item i.bianse5 {
            border: 1px solid #ddd;
            color: #fd4d46;
        }

        section#about .about-item i.bianse6 {
            border: 1px solid #ddd;
            color: #25e2dc;
        }

        section#about .about-item:hover i.bianse1 {
            color: #ffffff;
            background: #1f7dfc;
            background: -webkit-linear-gradient(top, #6fb4fa, #1073fc);
            background: -o-linear-gradient(top, #6fb4fa, #1073fc);
            background: -moz-linear-gradient(top, #6fb4fa, #1073fc);
            background: linear-gradient(top, #6fb4fa, #1073fc);
        }

        section#about .about-item:hover i.bianse2 {
            color: #ffffff;
            background: #9a71e9;
            background: -webkit-linear-gradient(top, #9a6fe8, #9696f1);
            background: -o-linear-gradient(top, #9a6fe8, #9696f1);
            background: -moz-linear-gradient(top, #9a6fe8, #9696f1);
            background: linear-gradient(top, #9a6fe8, #9696f1);
        }

        section#about .about-item:hover i.bianse3 {
            color: #ffffff;
            background: #fc9137;
            background: -webkit-linear-gradient(top, #fcc250, #fc8435);
            background: -o-linear-gradient(top, #fcc250, #fc8435);
            background: -moz-linear-gradient(top, #fcc250, #fc8435);
            background: linear-gradient(top, #fcc250, #fc8435);
        }

        section#about .about-item:hover i.bianse4 {
            color: #ffffff;
            background: #79e3c9;
            background: -webkit-linear-gradient(top, #a8f7d4, #72e0c8);
            background: -o-linear-gradient(top, #a8f7d4, #72e0c8);
            background: -moz-linear-gradient(top, #a8f7d4, #72e0c8);
            background: linear-gradient(top, #a8f7d4, #72e0c8);
        }

        section#about .about-item:hover i.bianse5 {
            color: #ffffff;
            background: #fd4d46;
            background: -webkit-linear-gradient(top, #fe9f7e, #fd4b45);
            background: -o-linear-gradient(top, #fe9f7e, #fd4b45);
            background: -moz-linear-gradient(top, #fe9f7e, #fd4b45);
            background: linear-gradient(top, #fe9f7e, #fd4b45);
        }

        section#about .about-item:hover i.bianse6 {
            color: #ffffff;
            background: #25e2dc;
            background: -webkit-linear-gradient(top, #99f1ed, #25e2dc);
            background: -o-linear-gradient(top, #99f1ed, #25e2dc);
            background: -moz-linear-gradient(top, #99f1ed, #25e2dc);
            background: linear-gradient(top, #99f1ed, #25e2dc);
        }

        /*section#about .about-item:hover h3 {color: #0f67b9;}*/
        .row {
            margin-right: -15px;
            margin-left: -15px;
            display: table;
            content: " ";
        }

        .col-md-2 {
            width: 16.66666667%;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
            float: left;
        }
    </style>
    <section id="about" class="tabs" style="">
        <div class="container">

            <div class="">
                <div class="section-title" style="margin-bottom: 50px;text-align: center">
                    <div style="font-size: 30px;">{{env('APP_NAME')}} 智慧酒店是什么？</div>
                    <p>{{env('APP_NAME')}} 智慧酒店是一款针对酒店、民宿的线上会员预定、服务、营销为一体的酒店管理系统，<br>酒店单商户版适用于酒店单店、连锁店的使用场景，酒店多商户版适用于加盟连锁店、运营酒店平台的使用场景。
                    </p>
                </div>
            </div>
            <div class="row">
                <div style="clear:both"></div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect2 active animated fadeInRight">
                        <i class="fa fa-home fa-2x bianse1"></i>
                        <h3>订房体系</h3>
                        <p>互联网线上订房+线下pms订房，实现房态同步无忧</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect5 active animated fadeInRight">
                        <i class="fa fa-cubes fa-2x bianse2"></i>
                        <h3>模式体系</h3>
                        <p>普通单酒店、直营连锁酒店、加盟连锁酒店、酒店平台运营等使用场景</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect5 active animated fadeInUp">
                        <i class="fa fa-group fa-2x bianse3"></i>
                        <h3>会员体系</h3>
                        <p>自定义多级会员和会员折扣，充值送优惠券、送积分、送余额</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect1 active animated fadeInUp">
                        <i class="fa fa-gift fa-2x bianse4"></i>
                        <h3>营销体系</h3>
                        <p>砍价、锦鲤抽奖、限时抢购、特价精品、转盘抽奖和多功能活动专栏等</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect1 active animated fadeInLeft">
                        <i class="fa fa-sitemap fa-2x bianse5"></i>
                        <h3>代理体系</h3>
                        <p>省市县代模式、常规代理模式、二级分销模式、平台多开等多种代理模式</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <div class="about-item scrollpoint sp-effect1 active animated fadeInLeft">
                        <i class="fa fa-podcast fa-2x bianse6"></i>
                        <h3>硬件体系</h3>
                        <p>智能门锁、人脸识别等智能硬件，并将持续对接新的智能硬件</p>
                    </div>
                </div>
            </div>
        </div>
        <br/><br/><br/>
    </section>

    <!-- 产品推荐 -->
    <div class="layout" style="background:#e6e6e6 ">
        <div class="line">
            <div class="blank-middle"></div>
            <div class="title-c text-center">
                <h3>产品展示</h3>
            </div>
        </div>

        <div class="container tab-normal">
            <div class="line tab">

                <div class="tab-head text-center">
                    <ul class="tab-nav">

                        <li class="active"><a href="#tab-1">单商户版</a></li>
                        <li class=""><a href="#tab-2">连锁店版</a></li>
                        <li class=""><a href="#tab-3">多商户版</a></li>
                    </ul>
                </div>
                <div class="blank-middle"></div>
                <div class="tab-body">
                    <div class="tab-panel active" id="tab-1">
                        <div class="line">
                            <div class="owl-carousel dot-center carousel-pro">
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index1.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-panel" id="tab-2">
                        <div class="line">
                            <div class="owl-carousel dot-center carousel-pro">
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-panel" id="tab-3">
                        <div class="line">
                            <div class="owl-carousel dot-center carousel-pro">
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item media media-y bg-white">
                                    <div class="padding-large">
                                        <a href="#" title=""><img src="/images/pc_index3.jpg"
                                                                  class="img-responsive"></a>
                                        <div class="media-body text-center">
                                            <h2><a href="#" class="height-middle text-main text-middle"></a></h2>
                                            <div class="margin-big-top margin-bottom">
                                                <a href="#"
                                                   class="button border-none radius-rounded text-center text-white">更多<i
                                                            class="fa fa-angle-right margin-small-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="blank-large"></div>
    </div>
    <!-- 产品推荐 -->

    <!-- 关于我们begin	 -->
    <div class="layout home-about" style="background-image:url({{asset('style1/images/indpic.jpg')}})">
        <div class="blank-big"></div>
        <div class="title-c text-center">
            <h3 class="text-white">公司简介</h3>
        </div>
        <div class="container">
            <div class="line bg-white">
                <div class="x6">
                    <div class="padding-large">
                        <!--公司简介——开始-->
                        <h2 class="text-main">深圳市融宝科技有限公司</h2>
                        <p class="text-sub text-default height-big">
                            融宝易住 智慧酒店预定 系统隶属于深圳市融宝科技有限公司，致力于轻松建站，不让用户添堵的理念，坚持按时更新迭代，推护好系统的安全及用户体验，保障好用户的建站需求。企业网站，专注中小型企业信息传播解决方案，利用网络传递信息在一定程度上提高了办事的效率，提高企业的竞争力。某某网站建设系统适合做各行各业网站，是一个自由和开放源码的网站建设系统，它是一个可以独立部署于客户服务器使用的网站建设系统，安全隐私性更好，不用担...
                        </p>
                        <div class="blank-small"></div>
                        <a href="#" class="button radius-rounded bg-yellow">查看更多<i
                                    class="fa fa-angle-right margin-small-left"></i></a>
                        <!--公司简介——结束-->
                    </div>
                </div>
                <div class="x6">
                    <img src="{{asset('style1/images/aboutpic.jpg')}}" class="img-responsive">
                </div>
            </div>
        </div>
        <div class="blank-large"></div>
    </div>
    <!-- 关于我们begin	 -->

    <!-- 新闻动态 -->
    @if($banner_new)
        <div class="layout bg-gray">
            <div class="container">
                <div class="line">
                    <div class="blank-big"></div>
                    <div class="title-c text-center">
                        <h3>新闻动态</h3>
                    </div>
                    <div class="x6">
                        <div class="home-news-l">
                            <div class="home-news-h">
                                <div class="media-img">
                                    <img src="{{$banner_new->litpic ?? ''}}" alt="" class="img-responsive img-new-h">
                                    <div class="post-title">
                                        <h3><a href="{{url('articleView')."/".$banner_new->id}}"
                                               target="_blank">{{$banner_new->title}}</a></h3>
                                        <p class="padding-top text-gray">{{date('Y-d-m',strtotime($banner_new->created_at))}}</p>
                                        <a href="" class="button radius-none border-none text-center hidden-l"><i
                                                    class="fa fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x6">
                        <div class="home-news-r">
                            @foreach ($new_list as $key => $items)
                                @if($key == 0)
                                    @continue;
                                @endif
                                <div class="line home-news-c margin-bottom bg-white">
                                    <div class="x3">
                                        <a href="{{url('articleView')."/".$items->id}}" title=""><img
                                                    src="{{$items->litpic}}" class="img-responsive img-new-w"
                                                    alt=""></a>
                                    </div>
                                    <div class="x2 text-center">
                                        <div class="news-time Conv_DINCondensedC">
                                            <p class="text-large text-sub">{{date('d',strtotime($items->created_at))}}</p>
                                            <p class="text-middle text-gray">{{date('Y-d',strtotime($items->created_at))}}</p>
                                        </div>
                                    </div>
                                    <div class="x7">
                                        <div class="news-title">
                                            <h2><a href="{{url('articleView')."/".$items->id}}"
                                                   class="text-main">{{$items->title}}</a></h2>
                                            <p class="text-gray hidden-l">{{$items->subtitle}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="blank-large"></div>
        </div>
    @endif
    <!-- 新闻动态 -->
    <script src="{{asset('style1/Lib/OwlCarousel2.21/owl.carousel.min.js') }}"></script>
@endsection

@push('js')

@endpush