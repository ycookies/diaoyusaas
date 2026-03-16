<ul class="nav flex-column">
    @if($ranking1)
        @foreach ($ranking1 as $key => $item)
        <li class="nav-item">
            <span class="nav-link">
                <span class="badge badge-danger">{{($key+1)}}</span>
                {{ !empty($item->first_name) ? substr($item->first_name,0,9).'***'.substr($item->first_name,15):'' }}
                <span class="float-right">{{$item->case_num}}件</span>
            </span>
        </li>
        @endforeach
    @endif
</ul>