@include('ycookies.api-tester::subview.table-toolbar')

{!! $grid->renderFilter() !!}

{!! $grid->renderHeader() !!}
<br/>
@foreach($grid->rows() as $row)
<div class="card card-widget">
    <div class="card-header with-border">
        <div class="user-block">
            <img class="img-circle" src="{!! $row->column('avatar') !!}" onerror="this.src='https://hotel.rongbaokeji.com/img/toux1.png'" alt="User Image">
            <span class="username"><a href="{{admin_url('user-member?_search_='.$row->column('user_id'))}}">{!! $row->column('user.nick_name') !!}</a></span>
            <span class="description">{{$row->column('updated_at')}}</span>
        </div>
        <!-- /.user-block -->
        <div class="card-tools">
            <div>
                推荐上首页:{!! $row->column('recommend') !!}
            </div>
            <div style="text-align: right;margin-top: 5px;">
                {!! $row->column(Dcat\Admin\Grid\Column::ACTION_COLUMN_NAME) !!}
            </div>

            {{--<button type="button" class="btn btn-tool" title="Mark as read">
                <i class="fa fa-circle"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fa fa-times"></i>
            </button>--}}
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row ">

            <div class="col-md-12">
                <div>
                    <div> {{$row->column('room.name')}}</div>
                    <div class="f12 text-gray">{{$row->column('ruzhu_date')}}</div>
                    @if(!empty($row->column('order_sn')))<div> <a href="{{admin_url('/booking-order?_search_='.$row->column('order_sn'))}}">订单号:{{$row->column('order_sn')}}</a></div>@endif
                </div>
                <div>评价星级:
                    @for($i=1;$i<=$row->score;$i++)
                    <i class="fa fa-star text-danger"></i>
                    @endfor
                </div>
                <br/>
                <p>{!! $row->content !!}</p>
                <p>
                    {!! $row->img !!}
                </p>
            </div>
        </div>


        {{--<button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i> Share</button>
        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-thumbs-up"></i> Like</button>
        <span class="float-right text-muted">127 likes - 3 comments</span>--}}
    </div>

    {{--<div class="card-footer">
        <form action="#" method="post">
            <img class="img-fluid img-circle img-sm" src="https://adminlte.io/themes/v3/dist/img/user4-128x128.jpg" alt="Alt Text">
            <div class="img-push">
                <input type="text" class="form-control form-control-sm" placeholder="Press enter to post comment">
            </div>
        </form>
    </div>--}}
</div>
@endforeach

{!! $grid->renderFooter() !!}