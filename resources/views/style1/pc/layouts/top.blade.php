<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--首页seo标题-->
    <title>{{$web_base['web_name'] ?? ''}} | @yield('title')</title>
    <!--首页seo描述-->
    <meta name="description" content="{{$web_base['web_description'] ?? ''}}" />
    <!--首页seo关键词-->
    <meta name="keywords" content="{{$web_base['web_keywords'] ?? ''}}" />
    <!--网站地址栏图标-->
    <link href="" rel="shortcut icon" type="image/x-icon" />
    <link href="{{asset('style1/style/pintuer.css') }}" rel="stylesheet">
    <link href="{{asset('style1/style/header.css') }}" rel="stylesheet">
    <link href="{{asset('style1/style/style.css') }}" rel="stylesheet">

    {{--<link href="https://www.hnjueqi.com/Public/css/bootstrap.min.css" rel="stylesheet">--}}
    <link href="{{asset('style1/Lib/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
</head>
<body>
<!--网站头部导航-->
@include('style1.pc.layouts.header')

<!--网站地址栏图标-->
@yield('content')

<!--网站尾部-->
@include('style1.pc.layouts.footer')


<script src="{{asset('style1/js/pintuer.js') }}"></script>
<script src="{{asset('style1/js/common.js') }}"></script>
<!-- Owl Carousel -->
{{--
<link href="{{asset('style1/Lib/OwlCarousel2.21/owl.carousel.min.css') }}" rel="stylesheet">
--}}

{{--
<script src="{{asset('style1/Lib/OwlCarousel2.21/owl.carousel.min.js') }}"></script>
--}}
<script src="{{asset('style1/Lib/OwlCarousel2.21/custom.js') }}"></script>
<script src="{{asset('style1/Lib/clipboard/clipboard.min.js') }}"></script>
</body>
<html>