<ul class="nav flex-column">
    @if($ranking3)
        @foreach ($ranking3 as $key => $item)
            <li class="nav-item">
            <span class="nav-link">
                <span class="badge badge-danger">{{($key+1)}}</span>
                {{ !empty($item->first_name) ? substr($item->first_name,0,9).'***'.substr($item->first_name,15):'' }}
                <span class="float-right"><strong>{{getNumberTxt(($item->compensate_amount * 3.5),0)}}+元</strong></span>
            </span>
            </li>
        @endforeach
    @endif
</ul>