<div class="btn-group default shadow-0" data-toggle="buttons">
    @foreach($submenu as $option => $label)
        <label class="btn btn-light  btn-sm {{ \Request::get('gender', 'all') == $option ? 'active' : '' }}">
            <input style="position: absolute;clip: rect(0,0,0,0);pointer-events: none;margin: 4px 0 0;line-height: normal;" type="radio" class="user-gender" value="{{ $option }}">{{$label['menu_name']}}
        </label>
    @endforeach
</div>

{{--
<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
    <div class="btn-group mr-2" role="group" aria-label="First group">
        <a type="button" class="btn btn-secondary">1</a>
        <a type="button" class="btn btn-secondary">2</a>
        <a type="button" class="btn btn-secondary">3</a>
        <a type="button" class="btn btn-secondary">4</a>
    </div>
</div>--}}
