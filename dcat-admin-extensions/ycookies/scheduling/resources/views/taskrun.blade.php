<div class="page-content">
	<div class="extension-demo">
		命令：{{$task}}
	</div>

	<div class="box box-default output-box hide">
		<div class="box-header with-border">
			<i class="fa fa-terminal"></i>
			{{--<div style="width:60px;" class='btn btn-block bg-gradient-success btn-xs run-task'>执行</div>--}}
			<h3 class="box-title">结果输出</h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<pre class="output-body">正在执行中...</pre>
		</div>
		<!-- /.box-body -->
	</div>
</div>


<style>
	.extension-demo {
		color: @primary;
	}
	.page-content{
		padding: 20px;
	}
	.output-body {
		white-space: pre-wrap;
		background: #000000;
		color: #00fa4a;
		padding: 10px;
		border-radius: 0;
	}
	.box-header{
		justify-content:start;
	}
</style>

<script data-exec-on-popstate>

</script>

<script require="@ycookies.scheduling">

	function LA() {}
	LA.token = "{{e(csrf_token(), false)}}";

	$(function () {
		NProgress.start();
		var id = '{{$taskid}}';
		$.ajax({
			method: 'POST',
			url: '{{ url('admin/scheduling-task-run') }}',
			data: {id: id, _token: LA.token},
			success: function (data) {
				if (typeof data === 'object') {
					$('.output-box').removeClass('hide');
					$('.output-box .output-body').html(data.data);
				}
				NProgress.done();
			}
		});
	});
	$('.extension-demo').extensionDemo();
</script>
