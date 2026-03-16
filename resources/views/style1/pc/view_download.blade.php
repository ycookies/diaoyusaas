@extends('style1.pc.layouts.top')
@section('title','详情页')
@section('content')

<div class="layout bg-gray">
    <div class="container hidden-l">
        <div class="line">
            <div class="nav-bread">
                <i class="fa fa-home margin-small-right text-gray" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="line">
            <div class="x9">
                <div class="sidebar-l">
                    <!--内容 begin-->
                    <div class="bg-white padding-large radius-middle">
                        <div class="article">
                            <div class="post">
                                <h1>{{$info->title}}</h1>
                                <div class="blank-small"></div>
                            </div>
                            <div class="line">
                                <div class="xl12 xs10 xm10 xb10">
                                    <div class="text-gray text-default height-big">
                                        <span class="margin-right">作者：{{$info->author}}</span>
                                        <span class="margin-right">发布时间：{{$info->created_at}}</span>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            <div class="blank-middle"></div>
                            <div class="post">
                                {!! $info->content !!}
                            </div>
                        </div><!-- article   -->
                        <div class="blank-small"></div>

                        <div class="blank-middle"></div>



                    </div>
                    <div class="blank-big"></div>
                </div><!-- sidebar-l -->
            </div>
            <div class="xl12 xs12 xm3 xb3">
                <div class="sidebar-r">
                    <div class="download-detail bg-white radius-middle">
                        <div class="padding-big">

                            <div class="blank-middle"></div>
                            <div class="text-center">
                                <div class="xl4 xs4 xb4 xm4">
                                    <span class="text-gray text-default">
                                    <i class="fa fa-eye margin-small-right"></i>
                                        {{$info->arcclick}}
                                    </span>
                                </div>
                                <div class="xl4 xs4 xb4 xm4">
                                    <span class="text-gray text-middle">
                                    <i class="fa fa-cloud-download margin-small-right"></i>
                                    {{$info->downcount}}
                                    </span>
                                </div>
                                <div class="xl4 xs4 xb4 xm4">
                                    <span class="text-gray text-middle">
                                        <i class="fa fa-heart margin-small-right"></i>
                                        <span>{{ $info->collectnum }}</span>
                                    </span>
                                </div>
                            </div>

                        <div class="blank-small"></div>
                        </div>
                    </div>
                    <div class="blank-small"></div>
                    <div class="padding-big bg-white radius-middle hidden-l">
                        <div class="title-l">
                            <h2>标签</h2>
                        </div>
                        <div class="blank-small"></div>

                    </div>

                    <div class="blank"></div>


                </div>
            </div>
        </div>
    </div>
    <div class="blank-big"></div>
</div>


@endsection
@push('js')
    <script>
        var clipboard = new ClipboardJS('.btn'); //先实例化
        clipboard.on('success', function(e) {
            alert('提取码复制成功，确定后转到下载地址！'); // 复制成功的事件
        });
        clipboard.on('error', function(e) {
            alert('复制失败'); // 复制失败的事件
        });
    </script>
@endpush