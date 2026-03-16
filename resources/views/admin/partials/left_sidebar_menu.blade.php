@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');

    $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');
    $topcheng = empty($item['parent_id']) ? true:false;
@endphp

@if($builder->visible($item))
        @if(empty($item['children']))
            <li class="nav-item">
                <a data-id="{{ $item['id'] ?? '' }}" @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
                href="{{ $builder->getUrl($item['uri']) }}"
                   class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
                    {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: $defaultIcon }}"></i>
                    <p>
                        {!! $builder->translate($item['title']) !!}
                    </p>
                </a>
            </li>
        @else

            <li class="{{ $horizontal ? 'dropdown' : 'has-treeview' }} {{ $depth > 0 ? 'dropdown-submenu' : '' }} nav-item {{ $builder->isActive($item) ? 'menu-open' : '' }} {{$topcheng ? 'hide-menu':''}}" id="parent_id_{{$item['id']}}">
                <a href="#"  data-id="{{ $item['id'] ?? '' }}"
                   class="nav-link {{ $builder->isActive($item) ? ($horizontal ? 'active' : '') : '' }}
                   {{ $horizontal ? 'dropdown-toggle' : '' }} {{ $item['parent_id'] == 0 ? 'nav-parent-0':''}} " @if($item['parent_id'] == 0) style="padding: 0px !important;" @endif >
                    {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: $defaultIcon }}"></i>
                    <p>
                        {!! $builder->translate($item['title']) !!} {{--- {{$item['parent_id']}}--}}

                        @if(! $horizontal)
                            <i class="right fa fa-angle-left"></i>
                        @endif
                    </p>
                </a>
                <ul class="nav {{ $horizontal ? 'dropdown-menu' : 'nav-treeview' }} menu-open">
                    @foreach($item['children'] as $item_sub)
                        @php
                            $item_sub['depth'] = $depth + 1;
                        @endphp
                        @include('admin.partials.left_sidebar_menu', ['item' => $item_sub,'builder'=>$builder])
                    @endforeach
                </ul>
            </li>
        @endif
@endif
