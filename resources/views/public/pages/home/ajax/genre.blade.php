@php
    use App\Helpers\Template;
@endphp
@if ($total > 0)
    @foreach ($items as $item)
        @php

            $thumbnail = $item['thumbnail'] ?? '';
            $thumbnailUrl = $thumbnail ? url("public/uploads/genres/{$thumbnail}") : '';
            $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
        @endphp
        <div class="gener-item">
            <div class="gener-inner">
                <a href="{{ rrt_route('public/market/index', ['genres' => $item['id'] ?? '']) }}" class="gener-thumb-wrap">
                    <img src="{{ $thumbnailUrl }}" class="gener-thumb">
                </a>
                <h3 class="gener-title"><a href="{{ rrt_route('public/market/index') }}">{{ $item['name'] ?? '' }}</a>
                </h3>
            </div>
        </div>
    @endforeach
@endif
