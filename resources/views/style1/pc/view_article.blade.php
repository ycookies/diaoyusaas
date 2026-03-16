@extends('style1.pc.layouts.top')
@section('title','详情页')
@section('content')
<div class="layout bg-gray">
	<div class="container">
		<div class="line">
			<div class="x12">
				<div class="nav-bread">
					<i class="fa fa-home margin-small-right" aria-hidden="true"></i>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="line">
			<div class="x9">
				<div class="sidebar-l">
					<!--内容 begin-->
					<div class="article">
						<div class="padding-large bg-white">
							<div class="post">
								<h1>{{$info->title}}</h1>
								<div class="blank"></div>
								<div class="text-gray padding-big-bottom text-default">
									<span class="margin-right">作者：{{$info->author}}</span>
									<span class="margin-right">发布时间：{{$info->created_at}}</span>
									<span class="margin-right">点击数：{{$info->click}}</span>

								</div>
								<hr class="bg-gray">
								<div class="blank"></div>
								<div id="content_html">
								{!! $info->content !!}
								</div>

							</div>
							<div class="blank-small"></div>
							<div class="text-center">
								<!-- 收藏代码开始  -->

								<!--  收藏代码结束 -->
							</div>
							<!--tag-->
							<div class="blank-middle"></div>

						</div>
					</div>
				</div>
				<div class="blank"></div>
			</div>
			<div class="x3">
				<div class="sidebar-r">
					<div class="bg-white padding-big">
						<div class="title-l">
							<h2>随便看看</h2>
						</div>
						<ul class="list-post-text">
							@foreach ($randlist as $item2)
							<li class="dot"><a href="{{url('articleView/'.$item2->id)}}" title="{{$item2->title}}" class="height"> {{$item2->title}}</a></li>
							@endforeach
						</ul>
					</div>
					<div class="blank-small"></div>

				</div>
			</div>
		</div><!--line end-->
	</div>
	<div class="blank-big"></div>
</div>


@endsection

@push('js')

@endpush