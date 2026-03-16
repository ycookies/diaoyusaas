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
	<div class="layout text-center bg-white">
		<div class="container">
			<div class="line">
				<ul class="nav-tree text-center">
					<li>
						<a href="#" class="active">全部</a>
					</li>
					@if($sub_typelist)
						@foreach ($sub_typelist as $item)
					<li>
						<a class="" href="{{url('listArticle').'/'.$item->id}}"> {{$item->title}}</a>
					</li>
						@endforeach
					@endif
				</ul>
			</div>
		</div>
	</div>

	<div class="layout bg-gray">
		{{--<div class="blank-small"></div>
		<div class="container">
			<div class="line">
				<div class="filter-box bg-white">
					<div class="padding-big">
					</div>
				</div>
			</div>
		</div>--}}
		<div class="blank-small"></div>
		<div class="container">
			<div class="line-big">
				<div class="product-list">
					@if(!$list->isEmpty())
						@foreach ($list as $item)
					<div class="x4">
						<div class="media media-y bg-white margin-big-bottom">
							<div class="padding-large">
								<a href="{{url('articleView/'.$item->id)}}" title="{{$item->title}}"><img src="{{$item->litpic}}" class="img-responsive"></a>
								<div class="media-body text-left">
									<h2><a href="{{url('articleView/'.$item->id)}}" class="text-middle">{{$item->title}}</a></h2>
									<!-- <p>{$field.seo_description}</p> -->
									<div class="price-info height margin-big-top">
										<ul>
											<li class="v2-1 text-yellow float-left">
												<span class="Conv_DINCondensedC text-big">￥{{$item->users_price}}</span>
											</li>
											<li class="v2-2 text-gray text-right float-right">
												<i class="fa fa-eye margin-small-right margin-left"></i>{{$item->click}}
											</li>
											<div class="clearfix"></div>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>


						@endforeach
					@else
						<div class="" style="text-align: center"><h4>暂无内容</h4></div>
					@endif
				</div>
				<!-- 分页 -->
				{{--<div class="blank-middle"></div>
				<div class="text-center">
					<ul class="pagination">

					</ul>
				</div>
				<div class="blank-large"></div>--}}
				<!-- 分页 -->
			</div>
		</div>
	</div>
@endsection

@push('js')

@endpush