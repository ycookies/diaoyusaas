<style>
    .sub-menu li a{
        padding: 10px 15px !important;
    }
</style>
<div class="card Dcat_Admin_Widgets_Card sub-menu">
    <div class="card-header with-border">
        <h3 class="card-title">导航</h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
            @foreach ($submenu as $item)
            <li class="nav-item">
                <a href="{{$item['uri'] ?? '#'}}" class="nav-link">
                    {{$item['menu_name']}}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
<style>
    .price_set:hover{
        color: #FFAA3E;
    }
</style>