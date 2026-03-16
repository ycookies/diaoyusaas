<table class="table custom-data-table data-table table-bordered complex-headers">
	<thead>
	<tr>
		<th>会员用户</th>
		<th>预订人</th>
		<th>预订人电话</th>
		<th>预定间数</th>
		<th>预定天数</th>
		<th>预定日期</th>
		<th>操作</th>
	</tr>

	</thead>
	<tbody>
	<tr>
		<td>
			<img src="{{$orderinfo->user->avatar}}" width="32"/>{{$orderinfo->user->nick_name}}
		</td>
		<td>{{$orderinfo->booking_name}}</td>
		<td>{{$orderinfo->booking_phone}}</td>
		<td>{{!empty($orderinfo->num) ? $orderinfo->num :'1' }} 间</td>
		<td>{{!empty($orderinfo->days) ? $orderinfo->days :'1' }} 天</td>
		<td>{{$orderinfo->arrival_time}} - {{$orderinfo->departure_time}}</td>
		<td></td>
	</tr>
	</tbody>
</table>

<table class="table custom-data-table data-table table-bordered complex-headers">
	<thead>
	<tr>
		<th>房型信息</th>
		<th>客房销售信息</th>
		<th>单价</th>
	</tr>

	</thead>
	<tbody>
	   <tr>
		   <td>
			   <div><img src="{{$orderinfo->room->logo}}" width="120"/></div>
			   <div> {{$orderinfo->room->name}} </div>
		   </td>
		   <td>
			   {{$orderinfo->roomsku->roomsku_title ?? ''}}
			   <div class="text-gray f12">早餐:{{$orderinfo->roomsku->roomsku_zaocan ?? '0'}} 份/天 </div>
			   @if(!empty($orderinfo->roomsku->roomsku_gift) && $orderinfo->roomsku->roomsku_gift != '[]')
				   <div class="text-gray f12">套餐礼包:{{$orderinfo->roomsku->roomsku_gift}} </div>
			   @endif
		   </td>
		   <td>{{$orderinfo->roomsku->roomsku_price ?? ''}}</td>

	   </tr>
	</tbody>
</table>