@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@if ($total > 0)
    @foreach ($items as $item)
        @php
            $file = $item['file'] ?? [];
            $user = $item['user'] ?? '';
            $username = $user['username'] ?? '';
            $contracts = $item
                ->listContracts()
                ->get()
                ->toArray();
            $price = Template::showTrackPrice($contracts);
            $linkProducer = Link::producerDetail($item->id);
        @endphp
      
        <div class="track-item track-inner">
            <a href="{{ rrt_route('public/track/detail', ['code' => $item->code, 'slug' => \Str::slug($item->name)]) }}">
                {!! Template::showTrackThumbnail($file) !!}
            </a>
            <div class="trac-text">
                <div class="track-meta">
                    <div class="track-meta-price">
                        {{ $price }}
                    </div>
                    @if (Template::showFreeDownload($item['id']))
                        <div class="track-meta-free">

                            <span>Free</span>
                            <i class="fas fa-download"></i>
                        </div>
                    @endif

                    <div class="track-meta-info">
                        {{ $item['bpm_number'] ?? 0 }} BM
                    </div>
                </div>
                <h3 class="track-title"><a
                        href="{{ rrt_route('public/track/detail', ['code' => $item->code, 'slug' => \Str::slug($item->name)]) }}">
                        {{ $item['name'] ?? '' }}</a></h3>
                <div class="track-author">
                    <a href="{{$linkProducer}}"> {{ $username }} </a>
                </div>
            </div>
        </div>
    @endforeach

@endif
