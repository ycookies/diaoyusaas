<table class="table custom-data-table data-table table-bordered complex-headers">
	<thead>
	<tr>
		<th>支付流水号</th>
		<th>来源</th>
		<th>支付状态</th>
		<th>总金额</th>
		<th>支付金额</th>
		<th>优惠金额</th>
		<th>支付时间</th>
		<th>操作</th>
	</tr>
	</thead>
	<tbody>
	   <tr>
		   <td>{{$orderinfo->trade_no}}</td>
		   <td>{{$orderinfo->type_txt}}</td>
		   <td>
			   @if($orderinfo->pay_status == 1)
				   <button class="btn btn-success btn-sm"> 已支付</button>
				   @else
				   <button class="btn btn-secondary btn-sm"> 未付款</button>
			   @endif
		   </td>
		   <td>{{$orderinfo->price}}</td>
		   <td>{{$orderinfo->total_cost}}</td>
		   <td>
			   @if($orderinfo->yyzk_cost > 0) <div> 普卡会员折扣:{{$orderinfo->yyzk_cost}} </div>@endif
			   @if($orderinfo->equitycard_cost > 0)<div> VIP权益卡折扣:{{$orderinfo->equitycard_cost}}</div>@endif
			   @if($orderinfo->dis_cost > 0)<div> 优惠券抵扣:{{$orderinfo->dis_cost}}</div>@endif
			   @if($orderinfo->hb_cost > 0)<div> 红包抵扣:{{$orderinfo->hb_cost}}</div>@endif
		   </td>
		   <td>{{$orderinfo->pay_time}}</td>
		   <td></td>
	   </tr>
	</tbody>
</table>
@if($orderinfo->status == 7 && !empty($orderinfo->refunds->refund_no))
<div style="margin-top: 10px"> <strong>退订退款</strong> </div>
<table class="table custom-data-table data-table table-bordered complex-headers">
	<thead>
	<tr>
		<th>退款单号</th>
		<th>交易流水号</th>
		<th>退款金额</th>
		<th>退款时间</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>{{$orderinfo->refunds->refund_no ?? ''}}</td>
		<td>{{$orderinfo->refunds->trade_no ?? ''}}</td>
		<td>{{$orderinfo->refunds->refund_fee ?? ''}}</td>
		<td>{{$orderinfo->refunds->refund_time ?? ''}}</td>
	</tr>
	</tbody>
</table>
@endif