@php
    use App\Helpers\Template;
@endphp
@if ($total > 0)
    @foreach ($items as $item)
        @php
            $firstName = $item['first_name'] ?? '';
            $lastName = $item['last_name'] ?? '';
            $username = $item['username'] ?? '';
            $fullName = $firstName ? "{$firstName} {$lastName}" : $username;
            $thumbnail = $item['thumbnail'] ?? '';
            $thumbnailUrl = $thumbnail ? url("public/uploads/users/{$thumbnail}") : '';
            $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
            $route_detail = rrt_route('public/market/index', [
                'name' => $item->username,
            ]);
        @endphp
        <a href="{{ $route_detail }}">
            <div class="producer-item">
                <div class="producer-inner">
                    <img src="{{ $thumbnailUrl }}" alt="" class="producer-thumb">
                    <div class="producer-text">
                        <h3 class="producer-title">{{ $fullName }}</h3>
                        <div class="producer-desc">
                            Inland Empire, CA
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
@endif
