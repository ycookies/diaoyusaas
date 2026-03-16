@extends('layouts.F7sellerRun')
@section('title','订房订单列表')
@section('content')
    <div class="pages" data-name="order-detail">
        <div class="page page-order-detail">
            @component('seller.component.navbar', ['title' => '订房订单列表'])
            @endcomponent
            <div class="page-content">
                订房订单列表
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush