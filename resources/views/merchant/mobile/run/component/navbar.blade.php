<div class="navbar">
    <div class="navbar-bg"></div>
    <div class="navbar-inner">
        <div class="left">
            <a class="link back">
                <i class="icon icon-back"></i>
            </a>
        </div>
        <div class="title">{{$title}}</div>
        @if(!empty($right_title))
        <div class="right">
            <a href="{{$right_link ?? ''}}" class="link">{{$right_title ?? ''}}</a>
        </div>
            @else
            <div class="right">
                <span style="color:#cccccc" onclick="reloadpage()"><i class="icon f7-icons if-not-md">arrow_2_circlepath</i></span>
            </div>
        @endif

    </div>
</div>