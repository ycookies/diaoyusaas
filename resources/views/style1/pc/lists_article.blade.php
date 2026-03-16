@extends('style1.pc.layouts.top')
@section('title','列表页')
@section('content')
    <!-- 频道banner,可在栏目图片里编辑 -->
    <div class="channel-banner"
         style="background-image: url(https://update.eyoucms.com/demo/uploads/allimg/20210106/1-2101061SQ3C4.jpg)">
        <div class="banner-info">
            <div class="container text-center">
                <h3 class="text-white">{{$info->title}}</h3>
                <p class="Conv_DINCondensedC text-white">{{$info->englist_name}}</p>
            </div>
        </div>
    </div>
    <!-- 频道banner end -->
    <!-- 横向栏目样式 begin -->
    <div class="nav-x">
        <div class="container">
            <div class="menu-toggle">
                <button class="button icon-navicon" data-target="#subnav">
                    <span><i class="fa fa-reorder margin-small-right"></i><!-- 上一级栏目名称 --></span>
                </button>
                <h3>top</h3><!-- 当前栏目名称 -->
                <ul class="nav-navicon text-center" id="subnav">
                    <li class="col-2">
                        <a href="#" class="active">全部</a>
                    </li>
                    @if($sub_typelist)
                        @foreach ($sub_typelist as $item)
                            <li class="col-2">
                                <a href="{{url('listArticle').'/'.$item->id}}" class="">{{$item->title}}</a>
                            </li>
                        @endforeach
                    @endif

                </ul>
            </div>
        </div>
    </div>
    <!-- 横向栏目样式 end -->
    <div class="layout bg-gray">
        <!--当前位置调用-->
        <div class="container hidden-l">
            <div class="line">
                <div class="nav-bread">
                    <i class="fa fa-home margin-small-right" aria-hidden="true"></i>首页
                </div>
            </div>
        </div>
        <!--当前位置调用 end-->
        <div class="blank"></div>
        <div class="container">
            <div id='block001'>
                @if(!$list->isEmpty())
                    @foreach ($list as $item)
                        <div class="line bg-white list-news">
                            <div class="x4">
                                <div class="media-img">
                                    <a href="{{url('articleView/'.$item->id)}}" title="">
                                        <img src="{{$item->litpic}}"
                                                              class="img-responsive" alt="{{$item->title}}"></a>
                                </div>
                            </div>
                            <div class="x1 text-center">
                                <div class="time text-center">
                                    <p class="text-large Conv_DINCondensedC">{{date('d',strtotime($item->created_at))}}</p>
                                    <p class="Conv_DINCondensedC text-middle text-gray">{{date('Y-d',strtotime($item->created_at))}}</p>
                                </div>
                            </div>
                            <div class="x6">
                                <div class="news margin-top">
                                    <h3><a href="{{url('articleView/'.$item->id)}}">{{$item->title}}</a></h3>
                                    <p class="text-gray hidden-l">{{$item->seo_description}}</p>
                                </div>
                            </div>
                            <div class="x1">
                                <a href="{{url('articleView/'.$item->id)}}" class="button radius-none text-center"><i class="fa fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="" style="text-align: center"><h4>暂无内容</h4></div>
                @endif
            </div>

            <!-- 瀑布流分页 -->
            @if(!$list->isEmpty())
            <div class="blank-small"></div>
            <div class="line">
                <div class="x12">
                    <div class="text-center">

                        <a href="javascript:void(0);"
                           class="button button-big bg-yellow radius-none text-center letter-spacing" {$field.onclick}>点击浏览更多<i
                                    class="fa fa-long-arrow-right text-big margin-big-left"></i></a>

                    </div>
                </div>
            </div>
            @endif
            <!-- 瀑布流分页 -->
        </div>
        <div class="blank-large"></div>
    </div>

@endsection

@push('js')

@endpush
