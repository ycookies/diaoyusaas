@extends('style1.pc.layouts.top')
@section('title','列表页')
@section('content')
    <!-- 频道banner,可在栏目图片里编辑 -->
    <div class="channel-banner"
         style="background-image: url(https://www.saishiyun.net/uploads/allimg/20210106/1-2101061SR5120.jpg)">
        <div class="banner-info">
            <div class="container text-center">
                <h3 class="text-white">{{$info->title}}</h3>
                <p class="Conv_DINCondensedC text-white">{{$info->englist_name}}</p>
            </div>
        </div>
    </div>
    <!-- 频道banner end -->

    <div class="layout bg-white">
        <div class="container">
            <div class="line nav-bread">
                <div class="x6">
                    <h2 class="text-main">{{$info->title}}</h2>
                </div>
                <div class="xm6 text-right">
                </div>
            </div>
        </div>
    </div>
    <div class="layout bg-gray">
        <div class="blank-big"></div>
        <div class="container">
            <div class="line">
                <ul class="normal-list">
                    @if(!$list->isEmpty())
                        @foreach ($list as $item)
                            <li class="dot">
                                <a href="{{url('articleView/'.$item->id)}}"
                                   title="{{$item->title}}">{{$item->title}}</a>
                                <span class="hidden-l">{{date('Y-m-d',strtotime($item->created_at))}}</span>
                            </li>
                        @endforeach
                    @else
                        <li class="dot">
                            <div class="" style="text-align: center"><h4>暂无内容</h4></div>
                        </li>
                    @endif
                </ul>
                <!-- 分页 -->
                <div class="blank-small"></div>
                <div class="text-center">
                    <ul class="pagination">

                    </ul>
                </div>
                <div class="blank-large"></div>
                <!-- 分页 -->
            </div>
        </div>
    </div>

@endsection

@push('js')

@endpush