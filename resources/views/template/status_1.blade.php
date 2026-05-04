@if (isset($status))
    @php
        $list = rrt_get_config_status();
        $currentStatus = isset($list[$status]) ? $list[$status] : $list['default'];
        $class = $currentStatus['class'] ?? '';
        $name = $currentStatus['name'] ?? '';
    @endphp
    <span class="track-status {{ $class }}">{{ $name }}</span>
@endif
