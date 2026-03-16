@extends('layouts.F7sellerRun')
@section('title','酒店运营')
@section('content')
    <div data-name="home" class="page seller-home">
        <div class="navbar">
            <div class="navbar-bg"></div>
            <div class="navbar-inner">
                <div class="title">酒店运营-工作台</div>
                <div class="right">
                    <span style="color:#cccccc" onclick="reload()"><i class="icon f7-icons if-not-md">arrow_2_circlepath</i></span>
                </div>
            </div>
        </div>
        <div class="toolbar  tabbar tabbar-icons toolbar-bottom">
            <div class="toolbar-inner">
                <a href="#tab-1" class="tab-link tab-link-active">
                    <i class="icon f7-icons if-not-md">wallet_fill</i>
                    <span class="tabbar-label">工作台</span>
                </a>
                {{--<a href="#tab-2" class="tab-link">
                    <i class="icon f7-icons if-not-md">doc_chart_fill</i>
                    <span class="tabbar-label">订单</span>
                </a>--}}
                {{--<a href="#tab-3" class="tab-link">
                    <i class="icon f7-icons if-not-md">bubble_left_bubble_right_fill</i>
                    <span class="tabbar-label">消息</span>
                </a>--}}
                <a href="#tab-4" class="tab-link">
                    <i class="icon f7-icons if-not-md">person_alt_circle_fill</i>
                    <span class="tabbar-label">我的</span>
                </a>
            </div>
        </div>

        <div class="page-content">
            <div class="tabs">
                <div id="tab-1" class=" tab tab-active">
                    @include('merchant.mobile.run.main.tab-1')
                </div>
                {{--<div id="tab-2" class=" tab">
                    2
                </div>--}}
                {{--<div id="tab-3" class=" tab">
                    3
                </div>--}}
                <div id="tab-4" class=" tab">
                    @include('merchant.mobile.run.main.tab-4')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
