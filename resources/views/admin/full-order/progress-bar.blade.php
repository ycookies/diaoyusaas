<div class="progress-bar-box">
	<div class="row">
		<div class="col-12">
			<div class="bs-stepper">
				<div class="bs-stepper-header" role="tablist">
					<!-- Step 1 -->
					<div class="step" data-target="#step1">
						<button type="button" class="step-trigger" role="tab">
									<span class="bs-stepper-circle">
										<span class="bs-stepper-active bg-primary">
											<i class="fa fa-check"></i>
										</span>
									</span>
							<span class="bs-stepper-label">订单创建</span>
							<small class="text-blue d-block datetimes">{{$orderinfo->created_at}}</small>
						</button>
					</div>
					<div class="line bg-blue"></div>

					<!-- Step 2 -->
					<div class="step" data-target="#step3">
						<button type="button" class="step-trigger" role="tab">
							@if($orderinfo->pay_status == 1)
									<span class="bs-stepper-circle">
										<span class="bs-stepper-active bg-primary">
											<i class="fa fa-check"></i>
										</span>
									</span>
							@else
								<span class="bs-stepper-circle">2</span>
							@endif
							<span class="bs-stepper-label">{{ $orderinfo->pay_status == 1 ? '已付款':'未付款' }}</span>
							<small class="text-blue d-block datetimes">{{$orderinfo->pay_time ?? ''}}</small>
						</button>
					</div>
					@if($orderinfo->pay_status == 1)
						<div class="line bg-blue"></div>
					@else
						<div class="line"></div>
					@endif

					<!-- 商家确认 -->
					@if($orderinfo->status != 7 && $orderinfo->pay_status == 1)
					<div class="step" data-target="#step2">
						<button type="button" class="step-trigger" role="tab">
							@if($orderinfo->is_confirm == 1)
									<span class="bs-stepper-circle">
										<span class="bs-stepper-active bg-primary">
											<i class="fa fa-check"></i>
										</span>
									</span>
							@else
								<span class="bs-stepper-circle">3</span>
							@endif
							<span class="bs-stepper-label">商家确认</span>
							@if($orderinfo->is_confirm == 1)<small class="text-blue d-block datetimes">{{$orderinfo->confirm_time}}</small>@endif
						</button>
					</div>
						@if($orderinfo->is_confirm == 1)
						    <div class="line bg-blue"></div>
						@else
							<div class="line"></div>
						@endif
					@endif

					<!-- 已退订 -->
					@if($orderinfo->status == 7)
					<div class="step" data-target="#step2">
						<button type="button" class="step-trigger" role="tab">
									<span class="bs-stepper-circle">
										<span class="bs-stepper-active bg-primary">
											<i class="fa fa-check"></i>
										</span>
									</span>
							<span class="bs-stepper-label">已退订</span>
							<small class="text-blue d-block datetimes">{{$orderinfo->created_at}}</small>
						</button>
					</div>
					<div class="line"></div>
					@endif
					<!-- Step 4 -->
					<div class="step" data-target="#step4">
						<button type="button" class="step-trigger" role="tab">
							<span class="bs-stepper-circle">4</span>
							<span class="bs-stepper-label">到店入住</span>
						</button>
					</div>
					<div class="line"></div>

					<!-- Step 5 -->
					<div class="step" data-target="#step5">
						<button type="button" class="step-trigger" role="tab">
							<span class="bs-stepper-circle">5</span>
							<span class="bs-stepper-label">离店结算</span>
						</button>
					</div>
					<div class="line"></div>

					<!-- Step 6 -->
					<div class="step" data-target="#step6">
						<button type="button" class="step-trigger" role="tab">
							<span class="bs-stepper-circle">6</span>
							<span class="bs-stepper-label">住店评价</span>
						</button>
					</div>

					<!-- Step 7 -->
					<div class="line"></div>
					<div class="step" data-target="#step6">
						<button type="button" class="step-trigger" role="tab">
							<span class="bs-stepper-circle">7</span>
							<span class="bs-stepper-label">完成</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<style>
.progress-bar-box{
	margin: 0px 50px;
}
.bs-stepper {
	width: 100%;
}

.bs-stepper .line {
	flex: 1;
	height: 2px;
	background-color: #e9ecef;
	margin: 0 1rem;
}

.bs-stepper-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 20px 0;
}
.datetimes{
	position: absolute;
	bottom: -15px;
	left: -35px;
	width: 120px;
}
.step {
	text-align: center;
	position: relative;
}
.bg-blue{
	background-color: #1079e2 !important;
}

.bs-stepper-circle,.bs-stepper-active {
	width: 30px;
	height: 30px;
	border-radius: 50%;
	background-color: #e9ecef;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 8px;
	color: #fff;
	font-weight: bold;
}
.bs-stepper-active{
	width: 20px !important;
	height: 20px !important;
	margin: 5px;
}
.bs-stepper-circle.bg-primary {
	background-color: #007bff;
}

.bs-stepper-label {
	font-weight: 500;
	margin-bottom: 0;
	display: block;
}

.output-body {
	white-space: pre-wrap;
	background: #000000;
	color: #00fa4a;
	padding: 10px;
	border-radius: 4px;
	margin: 0;
}

.step-trigger {
	border: none;
	background: none;
	padding: 0;
	cursor: default;
	position: relative;
}

small.text-muted {
	font-size: 12px;
}
</style>

<script require="@ycookies.scheduling">
	Dcat.ajax();
</script>
